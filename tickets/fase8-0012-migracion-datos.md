# Ticket: Ejecutar Migración de Datos Legacy

- **ID del Ticket:** `fase8-0012`
- **Fase:** `Sprint 8: Producción y Optimización`
- **Estado:** `Abierto`
- **Prioridad:** `Crítica`

---

## Descripción

Ejecutar migración completa de datos desde sistemas legacy al nuevo Sistema RRHH, incluyendo validación, reconciliación automática y procedimientos de rollback. Operación crítica para go-live.

---

## Criterios de Aceptación

- [ ] 335 empleados migrados exitosamente con 0 pérdida de datos
- [ ] Contratos históricos migrados con fechas y relaciones correctas
- [ ] Subsidios y anticipos históricos preservados
- [ ] Validación automática confirma 100% integridad de datos
- [ ] Scripts de reconciliación ejecutan sin discrepancias críticas
- [ ] Procedimientos de rollback probados y documentados
- [ ] Migración completa ejecuta en < 4 horas
- [ ] Checksums de validación coinciden entre origen y destino
- [ ] Backup pre-migración completado y verificado

---

## Detalles Técnicos y Notas de Implementación

### Fases de Migración
1. **Análisis**: Mapping completo de campos y validación de datos fuente
2. **Staging Test**: Migración de subset (10%) en ambiente staging
3. **Validation**: Scripts de reconciliación automática
4. **Production**: Migración por lotes con checkpoints
5. **Verification**: Validación final y sign-off

### Scripts de Migración por Lotes
```sql
-- ETL por lotes para performance
CREATE PROCEDURE sp_MigrarEmpleados
    @LoteId INT,
    @TamañoLote INT = 100
AS
BEGIN
    -- Log inicio
    INSERT INTO MigrationLog (Proceso, LoteId, Estado, FechaInicio)
    VALUES ('Empleados', @LoteId, 'Iniciado', GETDATE());
    
    -- Transformar y validar datos
    INSERT INTO Empleados (CI, Nombres, ApellidoPaterno, FechaNacimiento, Email, Estado)
    SELECT 
        TRIM(src.CI_NUMBER),
        TRIM(src.FIRST_NAME + ' ' + src.MIDDLE_NAME),
        TRIM(src.LAST_NAME),
        CONVERT(DATE, src.BIRTH_DATE),
        LOWER(TRIM(src.EMAIL)),
        1
    FROM staging.EmpleadosRaw src
    WHERE src.LoteId = @LoteId
    AND LEN(TRIM(src.CI_NUMBER)) >= 7;
    
    -- Log finalización
    UPDATE MigrationLog 
    SET Estado = 'Completado', RegistrosProcesados = @@ROWCOUNT
    WHERE Proceso = 'Empleados' AND LoteId = @LoteId;
END
```

### Validaciones y Reconciliación
```sql
-- Script de reconciliación automática
CREATE PROCEDURE sp_ValidarMigracion
AS
BEGIN
    -- Conteos por entidad
    SELECT 'Empleados' AS Entidad,
           (SELECT COUNT(*) FROM staging.EmpleadosRaw WHERE Estado = 'Activo') AS Origen,
           (SELECT COUNT(*) FROM Empleados WHERE Estado = 1) AS Destino;
    
    -- Checksums por muestra
    SELECT 'Checksum Test' AS Validacion,
           CASE WHEN EXISTS (
               SELECT 1 FROM (
                   SELECT TOP 100 CI, HASHBYTES('SHA256', CONCAT(CI, Nombres)) AS Hash
                   FROM Empleados ORDER BY IDEmpleado
               ) dest
               INNER JOIN staging.ChecksumEmpleados src ON dest.CI = src.CI 
               WHERE dest.Hash = src.Hash
           ) THEN 'PASS' ELSE 'FAIL' END AS Resultado;
END
```

### Rollback Strategy
```sql
-- Procedimiento de rollback por lotes
CREATE PROCEDURE sp_RollbackMigracion
    @Proceso NVARCHAR(50),
    @LoteId INT
AS
BEGIN
    -- Backup diferencial antes de rollback
    BACKUP DATABASE [RRHH_DB] TO DISK = 'C:\Backup\Pre_Rollback.bak' WITH DIFFERENTIAL;
    
    -- Eliminar registros del lote específico
    DELETE FROM Empleados 
    WHERE UsuarioCreacion = 'MIGRATION' 
    AND IDEmpleado IN (
        SELECT EntityId FROM MigrationLog 
        WHERE Proceso = @Proceso AND LoteId = @LoteId
    );
    
    -- Marcar rollback en log
    UPDATE MigrationLog 
    SET Estado = 'Rollback', FechaFin = GETDATE()
    WHERE Proceso = @Proceso AND LoteId = @LoteId;
END
```

---

## Especificaciones Relacionadas

- `/Docs/specs/data-migration.md` - Estrategia completa de migración
- `/Docs/specs/db-model.md` - Modelo de datos destino
- `/Docs/specs/runbooks.md` - Procedimientos operativos

---

## Dependencias

- **Bloquea:** Go-live del sistema
- **Bloqueado por:** `fase8-0011` (Observabilidad), todos los módulos core completados

---

## Sub-Tareas

- [ ] Finalizar mapping de campos origen → destino
- [ ] Crear scripts de transformación por entidad
- [ ] Implementar validaciones de integridad de datos
- [ ] Desarrollar scripts de reconciliación automática
- [ ] Crear procedimientos de rollback probados
- [ ] Ejecutar migración de prueba en staging
- [ ] Documentar ventana de migración y downtime
- [ ] Preparar comunicación a usuarios
- [ ] Ejecutar backup completo pre-migración
- [ ] Coordinar ventana de mantenimiento
- [ ] Ejecutar migración por lotes con validación
- [ ] Verificar integridad final y sign-off

---

## Comentarios y Discusión

**Owner:** [Placeholder - DBA + Senior Developer]
**Estimación:** 32-40 horas (incluyendo preparación y ejecución)
**Sprint:** Sprint 8 (Semanas 17-18)

**Ventana crítica**: Requiere coordinación con RRHH para ventana de mantenimiento de 4-6 horas en fin de semana.