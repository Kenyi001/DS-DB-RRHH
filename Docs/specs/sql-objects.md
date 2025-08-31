# Especificación de Objetos SQL - Sistema RRHH YPFB-Andina

## Propósito
Documentar todos los objetos SQL críticos (Stored Procedures, triggers, funciones, índices) necesarios para implementar la lógica de negocio y garantizar integridad de datos en el Sistema RRHH.

## Alcance
- Stored Procedures para cálculos de planilla y operaciones complejas
- Triggers para validaciones e invariantes de negocio
- Funciones de cálculo y validación
- Índices optimizados para performance
- Jobs de mantenimiento y backup

## Stored Procedures Críticos

### sp_GenerarPlanillaMensual
**Propósito**: Generar planilla mensual para todos los contratos activos del período.

```sql
CREATE OR ALTER PROCEDURE sp_GenerarPlanillaMensual
    @Mes INT,
    @Gestion INT,
    @Usuario NVARCHAR(100),
    @IdempotencyKey UNIQUEIDENTIFIER = NULL,
    @PlanillaId INT OUTPUT
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Validaciones iniciales
    IF @Mes NOT BETWEEN 1 AND 12 OR @Gestion < 2020
        THROW 50001, 'Mes o gestión inválidos', 1;
    
    -- Verificar idempotencia
    IF @IdempotencyKey IS NOT NULL AND EXISTS (
        SELECT 1 FROM LogPlanilla WHERE IdempotencyKey = @IdempotencyKey
    )
    BEGIN
        SELECT @PlanillaId = PlanillaId FROM LogPlanilla 
        WHERE IdempotencyKey = @IdempotencyKey;
        RETURN 0;
    END
    
    -- Adquirir lock exclusivo
    DECLARE @lockResource NVARCHAR(100) = CONCAT('planilla_', @Gestion, '_', @Mes);
    DECLARE @rc INT;
    EXEC @rc = sp_getapplock @Resource = @lockResource, @LockMode = 'Exclusive', @LockTimeout = 30000;
    
    IF @rc < 0
        THROW 50002, 'No se pudo adquirir bloqueo para el período', 1;
    
    BEGIN TRY
        BEGIN TRANSACTION;
        
        -- Insertar log de inicio
        INSERT INTO LogPlanilla (IdempotencyKey, Mes, Gestion, Usuario, FechaInicio, EstadoProceso)
        VALUES (@IdempotencyKey, @Mes, @Gestion, @Usuario, SYSUTCDATETIME(), 'Iniciado');
        
        SET @PlanillaId = SCOPE_IDENTITY();
        
        -- Procesar contratos en batches
        DECLARE @ContratosProcessados INT = 0;
        
        DECLARE contrato_cursor CURSOR FOR
        SELECT IDContrato FROM Contratos 
        WHERE Estado = 1 
        AND (@Mes BETWEEN MONTH(FechaInicio) AND MONTH(ISNULL(FechaFin, GETDATE())));
        
        DECLARE @IDContrato INT;
        OPEN contrato_cursor;
        
        FETCH NEXT FROM contrato_cursor INTO @IDContrato;
        WHILE @@FETCH_STATUS = 0
        BEGIN
            EXEC sp_CalcularSalarioMensual @IDContrato, @Mes, @Gestion;
            SET @ContratosProcessados = @ContratosProcessados + 1;
            FETCH NEXT FROM contrato_cursor INTO @IDContrato;
        END
        
        CLOSE contrato_cursor;
        DEALLOCATE contrato_cursor;
        
        -- Actualizar log de finalización
        UPDATE LogPlanilla 
        SET FechaFin = SYSUTCDATETIME(),
            EstadoProceso = 'Completado',
            ContratosProcessados = @ContratosProcessados
        WHERE PlanillaId = @PlanillaId;
        
        COMMIT TRANSACTION;
        
    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 ROLLBACK TRANSACTION;
        
        UPDATE LogPlanilla 
        SET EstadoProceso = 'Error',
            Observaciones = ERROR_MESSAGE()
        WHERE PlanillaId = @PlanillaId;
        
        THROW;
    END CATCH
    
    EXEC sp_releaseapplock @Resource = @lockResource;
END
```

### sp_CalcularSalarioMensual
**Propósito**: Calcular salario individual por contrato y período.

```sql
CREATE OR ALTER PROCEDURE sp_CalcularSalarioMensual
    @IDContrato INT,
    @Mes INT,
    @Gestion INT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @HaberBasico DECIMAL(10,2);
    DECLARE @TotalSubsidios DECIMAL(10,2) = 0;
    DECLARE @TotalDescuentos DECIMAL(10,2) = 0;
    
    -- Obtener haber básico del contrato
    SELECT @HaberBasico = HaberBasico 
    FROM Contratos WHERE IDContrato = @IDContrato AND Estado = 1;
    
    -- Calcular subsidios
    SELECT @TotalSubsidios = ISNULL(SUM(Monto), 0)
    FROM Subsidios 
    WHERE IDContrato = @IDContrato 
    AND Estado = 1
    AND @Gestion BETWEEN YEAR(FechaInicio) AND YEAR(ISNULL(FechaFin, GETDATE()));
    
    -- Calcular descuentos (anticipos)
    SELECT @TotalDescuentos = ISNULL(SUM(MontoAnticipo), 0)
    FROM Anticipos 
    WHERE IDContrato = @IDContrato 
    AND MesDescuento = @Mes 
    AND YEAR(FechaAprobacion) = @Gestion
    AND EstadoAnticipo = 'Aprobado';
    
    -- Upsert en GestionSalarios
    MERGE GestionSalarios AS target
    USING (SELECT @IDContrato, @Mes, @Gestion, @HaberBasico, @TotalSubsidios, @TotalDescuentos) 
        AS source (IDContrato, Mes, Gestion, HaberBasico, TotalSubsidios, TotalDescuentos)
    ON target.IDContrato = source.IDContrato 
        AND target.Mes = source.Mes 
        AND target.Gestion = source.Gestion
    WHEN MATCHED THEN
        UPDATE SET 
            HaberBasico = source.HaberBasico,
            TotalSubsidios = source.TotalSubsidios,
            TotalDescuentos = source.TotalDescuentos,
            LiquidoPagable = source.HaberBasico + source.TotalSubsidios - source.TotalDescuentos,
            FechaModificacion = SYSUTCDATETIME()
    WHEN NOT MATCHED THEN
        INSERT (IDContrato, Mes, Gestion, HaberBasico, TotalSubsidios, TotalDescuentos, LiquidoPagable, Estado)
        VALUES (source.IDContrato, source.Mes, source.Gestion, source.HaberBasico, 
                source.TotalSubsidios, source.TotalDescuentos, 
                source.HaberBasico + source.TotalSubsidios - source.TotalDescuentos, 'Generado');
END
```

## Triggers de Validación

### trg_Validar_Anticipo
**Propósito**: Validar que anticipos no excedan 50% del haber básico.

```sql
CREATE TRIGGER trg_Validar_Anticipo
ON Anticipos
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Validar tope 50%
    IF EXISTS (
        SELECT 1 FROM inserted i 
        INNER JOIN Contratos c ON i.IDContrato = c.IDContrato
        WHERE i.MontoAnticipo > (c.HaberBasico * 0.5)
    )
    BEGIN
        RAISERROR('El anticipo no puede superar el 50% del salario básico vigente.', 16, 1);
        RETURN;
    END;
    
    -- Validar contrato activo
    IF EXISTS (
        SELECT 1 FROM inserted i 
        INNER JOIN Contratos c ON i.IDContrato = c.IDContrato
        WHERE c.Estado <> 1
    )
    BEGIN
        RAISERROR('No se puede crear anticipo para contrato inactivo.', 16, 1);
        RETURN;
    END;
    
    -- Insertar si validaciones pasan
    INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaSolicitud, EstadoAnticipo, Usuario)
    SELECT IDContrato, MontoAnticipo, FechaSolicitud, EstadoAnticipo, Usuario
    FROM inserted;
END
```

### trg_Empleados_Audit
**Propósito**: Auditoría automática de cambios en empleados.

```sql
CREATE TRIGGER trg_Empleados_Audit
ON Empleados
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;
    
    INSERT INTO AuditLog (Entity, EntityId, Action, UserId, CreatedAt, PayloadBefore, PayloadAfter, TraceId)
    SELECT 'Empleados', 
           COALESCE(i.IDEmpleado, d.IDEmpleado),
           CASE 
               WHEN i.IDEmpleado IS NOT NULL AND d.IDEmpleado IS NULL THEN 'INSERT'
               WHEN i.IDEmpleado IS NOT NULL AND d.IDEmpleado IS NOT NULL THEN 'UPDATE'
               ELSE 'DELETE' 
           END,
           SYSTEM_USER,
           SYSUTCDATETIME(),
           (SELECT d.* FOR JSON PATH, WITHOUT_ARRAY_WRAPPER),
           (SELECT i.* FOR JSON PATH, WITHOUT_ARRAY_WRAPPER),
           NULL
    FROM inserted i 
    FULL OUTER JOIN deleted d ON i.IDEmpleado = d.IDEmpleado;
END
```

## Funciones de Cálculo

### fn_ValidarSolapeContrato
**Propósito**: Validar que no existan contratos superpuestos para un empleado.

```sql
CREATE FUNCTION fn_ValidarSolapeContrato(
    @IDEmpleado INT,
    @FechaInicio DATE,
    @FechaFin DATE,
    @IDContratoExcluir INT = NULL
)
RETURNS BIT
AS
BEGIN
    DECLARE @TieneSolape BIT = 0;
    
    IF EXISTS (
        SELECT 1 FROM Contratos
        WHERE IDEmpleado = @IDEmpleado
        AND Estado = 1
        AND (@IDContratoExcluir IS NULL OR IDContrato <> @IDContratoExcluir)
        AND (
            (@FechaInicio BETWEEN FechaInicio AND ISNULL(FechaFin, '2099-12-31'))
            OR (@FechaFin BETWEEN FechaInicio AND ISNULL(FechaFin, '2099-12-31'))
            OR (FechaInicio BETWEEN @FechaInicio AND @FechaFin)
        )
    )
        SET @TieneSolape = 1;
    
    RETURN @TieneSolape;
END
```

## Índices Optimizados

### Performance Crítica
```sql
-- Planilla: consultas por período
CREATE NONCLUSTERED INDEX IX_GestionSalarios_PagosPendientes
ON GestionSalarios (Gestion, Mes)
INCLUDE (IDContrato, LiquidoPagable, FechaPago);

-- Contratos: filtros por departamento y estado
CREATE NONCLUSTERED INDEX IX_Contratos_ActivoCargoDepto
ON Contratos (IDDepartamento, IDCargo, Estado)
INCLUDE (IDEmpleado, NumeroContrato, FechaInicio, FechaFin)
WHERE Estado = 1;

-- Auditoría: búsquedas por entidad y fecha
CREATE NONCLUSTERED INDEX IX_AuditLog_EntityDate
ON AuditLog (Entity, CreatedAt DESC)
INCLUDE (EntityId, Action, UserId, TraceId);

-- Vacaciones: solicitudes pendientes por jefe
CREATE NONCLUSTERED INDEX IX_SolicitudesVacaciones_JefePendientes
ON SolicitudesVacaciones (JefeAprobador, EstadoSolicitud, FechaInicio)
INCLUDE (IDContrato, FechaFin, DiasVacaciones);
```

## Jobs de Mantenimiento

### Backup y Mantenimiento
```sql
-- Job: Backup Full (diario 02:00)
BACKUP DATABASE [RRHH_DB] 
TO DISK = 'C:\Backup\RRHH_Full_$(ESCAPE_SQUOTE(STRTRAN(REPLACE(CONVERT(varchar, GETDATE(), 112), ' ', '_'), ':', '_'))).bak'
WITH COMPRESSION, CHECKSUM;

-- Job: Index Maintenance (semanal)
EXEC sp_MSforeachtable 'ALTER INDEX ALL ON ? REBUILD WITH (ONLINE = ON)';

-- Job: Statistics Update (diario)
EXEC sp_updatestats;

-- Job: Purge AuditLog (mensual - retener 90 días)
DELETE FROM AuditLog 
WHERE CreatedAt < DATEADD(DAY, -90, GETDATE());
```

## Métricas y Monitoreo

### Queries de Performance
```sql
-- Top 10 queries más lentas
SELECT TOP 10 
    qs.sql_handle,
    qs.total_elapsed_time / qs.execution_count AS avg_elapsed_time,
    qs.execution_count,
    SUBSTRING(st.text, (qs.statement_start_offset/2)+1,
        CASE WHEN qs.statement_end_offset = -1 
        THEN LEN(CONVERT(nvarchar(max), st.text)) * 2 
        ELSE qs.statement_end_offset - qs.statement_start_offset END /2) AS query_text
FROM sys.dm_exec_query_stats qs
CROSS APPLY sys.dm_exec_sql_text(qs.sql_handle) st
ORDER BY avg_elapsed_time DESC;

-- Uso de índices
SELECT 
    i.name AS IndexName,
    s.user_seeks,
    s.user_scans,
    s.user_lookups,
    s.user_updates
FROM sys.dm_db_index_usage_stats s
INNER JOIN sys.indexes i ON s.object_id = i.object_id AND s.index_id = i.index_id
WHERE s.database_id = DB_ID()
ORDER BY s.user_seeks + s.user_scans + s.user_lookups DESC;
```

## Configuración de Base de Datos

### Optimizaciones de Concurrencia
```sql
-- Habilitar snapshot isolation
ALTER DATABASE [RRHH_DB] SET READ_COMMITTED_SNAPSHOT ON;
ALTER DATABASE [RRHH_DB] SET ALLOW_SNAPSHOT_ISOLATION ON;

-- Configurar memory y parallelism
ALTER DATABASE [RRHH_DB] SET MAX_DOP = 4;
```

### Configuraciones de Backup
```sql
-- Modelo de recovery FULL para point-in-time recovery
ALTER DATABASE [RRHH_DB] SET RECOVERY FULL;

-- Backup policy
-- Full: diario 02:00, retención 14 días
-- Differential: cada 6 horas, retención 7 días  
-- Log: cada 15 minutos, retención 7 días
```

## Validaciones y Constraints

### Business Rules Implementation
```sql
-- Constraint: Fechas de contrato coherentes
ALTER TABLE Contratos 
ADD CONSTRAINT CK_Contratos_FechasCoherentes 
CHECK (FechaFin IS NULL OR FechaFin >= FechaInicio);

-- Constraint: Haber básico mínimo
ALTER TABLE Contratos 
ADD CONSTRAINT CK_Contratos_HaberMinimo 
CHECK (HaberBasico >= 2500); -- SMN aproximado

-- Constraint: Anticipo máximo 50%
-- (Implementado via trigger trg_Validar_Anticipo)
```

## Dependencias
- SQL Server 2019/2022 con permisos sysadmin para crear objetos
- SQL Server Agent habilitado para jobs automatizados
- Backup storage con espacio suficiente (estimado 10GB/mes)
- Monitoring tools con acceso a DMVs

## Criterios de Aceptación
- [ ] Todos los SPs compilan sin errores y tienen manejo robusto de excepciones
- [ ] Triggers validan reglas de negocio correctamente
- [ ] Índices optimizan queries críticas (plan de ejecución < 100ms)
- [ ] Jobs de backup ejecutan exitosamente y se pueden restaurar
- [ ] Tests de integración validan SPs con datos de prueba
- [ ] Auditoría registra todos los cambios en entidades críticas
- [ ] Application locks previenen condiciones de carrera
- [ ] Performance de sp_GenerarPlanillaMensual < 30s para 335 empleados
- [ ] Rollback automático funciona en caso de errores
- [ ] Métricas de DB exportadas a sistema de monitoreo

## Referencias al Documento Canónico
Este documento se basa en las secciones 6, 19, 20, 21 y 49 del [Project Chapter](../projectChapter.md). Para scripts completos, ejemplos de implementación y guías de optimización, consultar el documento principal.

**Supuestos:**
- DBA disponible para revisión y aprobación de objetos críticos
- Entorno de staging para testing de SPs antes de producción
- Monitoreo de performance implementado para optimización continua