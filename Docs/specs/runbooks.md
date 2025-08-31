# Especificación de Runbooks - Sistema RRHH YPFB-Andina

## Propósito
Documentar procedimientos operativos paso a paso para tareas críticas, resolución de incidentes y mantenimiento del Sistema RRHH en producción.

## Alcance
- Procedimientos de deployment y rollback
- Troubleshooting de problemas comunes
- Mantenimiento de base de datos
- Recuperación ante desastres
- Escalation procedures

## Runbook: Deployment Production

### Procedimiento de Despliegue Normal
**Duración estimada**: 15-20 minutos
**Ventana de mantenimiento**: No requerida para rolling updates

1. **Pre-deployment Checklist**
   - [ ] Pipeline CI verde en main branch
   - [ ] Backup diferencial completado exitosamente
   - [ ] Notificación a stakeholders (15min antes)
   - [ ] Health checks baseline documentados

2. **Deployment Steps**
   ```bash
   # 1. Tag release
   git tag v1.2.3
   git push origin v1.2.3
   
   # 2. Build and push image
   docker build -t rrhh-app:v1.2.3 .
   docker push registry.ypfb.local/rrhh-app:v1.2.3
   
   # 3. Update staging first
   docker-compose -f staging.yml pull
   docker-compose -f staging.yml up -d --no-deps app
   
   # 4. Run smoke tests on staging
   ./scripts/smoke-tests.sh staging
   
   # 5. Deploy to production if staging OK
   docker-compose -f production.yml pull
   docker-compose -f production.yml up -d --no-deps app
   ```

3. **Post-deployment Validation**
   - [ ] Health check `/health` returns 200
   - [ ] Database connections functional
   - [ ] Redis cache accessible
   - [ ] Critical endpoints responding < 3s
   - [ ] Worker queues processing jobs

### Rollback Procedure
**Trigger**: Health checks fail, P95 > 10s, error rate > 5%

```bash
# 1. Immediate rollback to previous image
docker-compose -f production.yml down app
docker-compose -f production.yml up -d app

# 2. Verify rollback success
curl -f http://localhost/health
./scripts/smoke-tests.sh production

# 3. Database rollback if needed
# (Only if destructive migrations were deployed)
php artisan migrate:rollback --step=1
```

## Runbook: Generación de Planilla

### Troubleshooting: Planilla No Se Genera
**Síntomas**: Timeout en `/api/v1/planilla/generar`, LogPlanilla muestra estado 'Error'

1. **Diagnóstico Inicial**
   ```sql
   -- Verificar estado actual
   SELECT TOP 5 * FROM LogPlanilla 
   ORDER BY FechaInicio DESC;
   
   -- Verificar locks activos
   SELECT * FROM sys.dm_tran_locks 
   WHERE resource_type = 'APPLICATION';
   
   -- Verificar procesos largos
   SELECT session_id, start_time, status, command, wait_type
   FROM sys.dm_exec_requests 
   WHERE total_elapsed_time > 30000;
   ```

2. **Resolución por Etapas**
   - **Lock Issue**: `EXEC sp_releaseapplock @Resource = 'planilla_YYYY_MM'`
   - **Timeout**: Aumentar timeout en Laravel config
   - **Data Issue**: Verificar integridad de contratos activos
   - **Resource**: Verificar CPU/memoria del servidor DB

3. **Regeneración Manual**
   ```php
   // Artisan command para regenerar
   php artisan planilla:generar --mes=8 --gestion=2025 --force
   ```

## Runbook: Database Maintenance

### Backup Verification (Semanal)
```sql
-- 1. Verificar último backup exitoso
SELECT TOP 5 
    database_name,
    backup_start_date,
    backup_finish_date,
    type,
    backup_size/1024/1024 AS size_mb
FROM msdb.dbo.backupset 
WHERE database_name = 'RRHH_DB'
ORDER BY backup_start_date DESC;

-- 2. Test restore en sandbox
RESTORE DATABASE RRHH_Test 
FROM DISK = 'C:\Backup\RRHH_Full_latest.bak'
WITH REPLACE, NORECOVERY;

-- 3. Smoke test en sandbox
USE RRHH_Test;
SELECT COUNT(*) FROM Empleados; -- Debe retornar ~335
EXEC sp_CalcularSalarioMensual 1, 8, 2025; -- No debe fallar
```

### Index Maintenance (Semanal)
```sql
-- 1. Identificar fragmentación
SELECT 
    OBJECT_NAME(i.object_id) AS TableName,
    i.name AS IndexName,
    s.avg_fragmentation_in_percent
FROM sys.dm_db_index_physical_stats(DB_ID(), NULL, NULL, NULL, 'LIMITED') s
INNER JOIN sys.indexes i ON s.object_id = i.object_id AND s.index_id = i.index_id
WHERE s.avg_fragmentation_in_percent > 10;

-- 2. Reorganize/Rebuild según fragmentación
-- >10% = REORGANIZE, >30% = REBUILD
ALTER INDEX IX_GestionSalarios_PagosPendientes ON GestionSalarios REORGANIZE;
```

## Runbook: Incident Response

### Severidad P0: Sistema No Disponible
**Response Time**: 15 minutos

1. **Immediate Actions**
   - Verificar health checks de todos los servicios
   - Revisar últimos deployments (rollback si necesario)
   - Verificar conectividad de base de datos
   - Escalar a equipo de infrastructure si persiste

2. **Communication**
   - Actualizar status page cada 15 minutos
   - Notificar a usuarios por email/Teams
   - Documentar timeline en incident tracker

### Severidad P1: Performance Degradada
**Response Time**: 1 hora

```sql
-- Diagnóstico de performance
-- 1. Top blocking queries
SELECT 
    r.session_id,
    r.start_time,
    r.status,
    r.command,
    r.wait_type,
    r.total_elapsed_time,
    t.text AS query_text
FROM sys.dm_exec_requests r
CROSS APPLY sys.dm_exec_sql_text(r.sql_handle) t
WHERE r.total_elapsed_time > 5000
ORDER BY r.total_elapsed_time DESC;

-- 2. Deadlocks recientes
SELECT * FROM sys.dm_xe_session_targets 
WHERE target_name = 'ring_buffer';
```

## Runbook: Database Recovery

### Procedimiento Point-in-Time Recovery
**Escenario**: Corrupción de datos o eliminación accidental

1. **Assessment**
   ```sql
   -- Verificar último backup válido
   RESTORE HEADERONLY FROM DISK = 'C:\Backup\RRHH_Full_latest.bak';
   
   -- Identificar punto de recovery target
   SELECT TOP 10 * FROM AuditLog 
   WHERE Entity = 'Empleados' AND Action = 'DELETE'
   ORDER BY CreatedAt DESC;
   ```

2. **Recovery Steps**
   ```sql
   -- 1. Restore full backup
   RESTORE DATABASE RRHH_Recovery 
   FROM DISK = 'C:\Backup\RRHH_Full_latest.bak'
   WITH REPLACE, NORECOVERY, MOVE 'RRHH_Data' TO 'C:\Data\RRHH_Recovery.mdf';
   
   -- 2. Apply differential
   RESTORE DATABASE RRHH_Recovery 
   FROM DISK = 'C:\Backup\RRHH_Diff_latest.bak'
   WITH NORECOVERY;
   
   -- 3. Apply log backups to point-in-time
   RESTORE LOG RRHH_Recovery 
   FROM DISK = 'C:\Backup\RRHH_Log_target.trn'
   WITH STOPAT = '2025-08-31 14:30:00', RECOVERY;
   ```

3. **Validation**
   ```sql
   USE RRHH_Recovery;
   -- Verificar integridad
   DBCC CHECKDB;
   
   -- Verificar data crítica
   SELECT COUNT(*) FROM Empleados WHERE Estado = 1;
   SELECT COUNT(*) FROM GestionSalarios WHERE Mes = 8 AND Gestion = 2025;
   ```

## Runbook: Performance Tuning

### Query Optimization
```sql
-- 1. Identificar queries problemáticas
SELECT TOP 10
    qs.sql_handle,
    qs.total_elapsed_time / qs.execution_count AS avg_time_ms,
    qs.execution_count,
    SUBSTRING(st.text, (qs.statement_start_offset/2)+1, 
        CASE WHEN qs.statement_end_offset = -1 
        THEN LEN(CONVERT(nvarchar(max), st.text)) * 2 
        ELSE qs.statement_end_offset - qs.statement_start_offset END /2) AS query_text
FROM sys.dm_exec_query_stats qs
CROSS APPLY sys.dm_exec_sql_text(qs.sql_handle) st
ORDER BY avg_time_ms DESC;

-- 2. Analizar planes de ejecución
SELECT * FROM sys.dm_exec_query_plan(0x...); -- sql_handle de query problemática
```

### Index Optimization
- Revisar missing indexes sugeridos por SQL Server
- Analizar unused indexes para eliminar overhead
- Verificar estadísticas actualizadas automáticamente

## Escalation Matrix

### Contactos por Severidad
- **P0 (Sistema Down)**: DevOps Lead + DBA + Product Owner
- **P1 (Performance)**: Dev Team + DBA
- **P2 (Feature Issues)**: Dev Team
- **P3 (Enhancement)**: Product Backlog

### Horarios de Soporte
- **Business Hours**: 8:00-18:00 (respuesta 4h)
- **After Hours**: Solo P0/P1 (respuesta 2h)
- **Weekends**: Solo P0 (respuesta 1h)

## Dependencias
- Acceso a herramientas de monitoreo (Grafana, Prometheus)
- Permisos de administrador en servidores de aplicación
- Acceso DBA para operaciones de base de datos
- Credenciales para servicios externos (PagerDuty, Teams)

## Criterios de Aceptación
- [ ] Runbooks probados en entorno staging
- [ ] Tiempos de respuesta validados para cada procedimiento
- [ ] Scripts automatizados funcionando correctamente
- [ ] Escalation matrix actualizada con contactos vigentes
- [ ] Documentación accessible 24/7 por equipo operativo
- [ ] Procedimientos de rollback validados < 5 minutos
- [ ] Recovery procedures probados mensualmente
- [ ] Alertas configuradas con runbooks asociados
- [ ] Training completado para equipo de soporte
- [ ] Post-mortem template definido para incidents

## Referencias al Documento Canónico
Este documento se basa en las secciones 8, 21, 51 y 56 del [Project Chapter](../projectChapter.md). Para scripts específicos, configuraciones detalladas y procedimientos completos, consultar el documento principal.

**Supuestos:**
- Equipo de DevOps/SRE disponible 24/7 para P0
- Herramientas de incident management configuradas
- Acceso a backups y sistemas de recovery