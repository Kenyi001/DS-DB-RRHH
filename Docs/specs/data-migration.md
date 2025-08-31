# Especificación de Migración de Datos - Sistema RRHH YPFB-Andina

## Propósito
Definir la estrategia completa para migrar datos existentes de sistemas legados al nuevo Sistema RRHH, garantizando integridad, trazabilidad y capacidad de rollback durante el proceso.

## Alcance
- Análisis y mapeo de datos fuente
- Scripts de transformación y carga
- Validación y reconciliación automatizada
- Estrategia de rollback y contingencia
- Plan de ejecución por fases

## Análisis de Datos Fuente

### Inventario de Sistemas Legados
- **Sistema A**: Excel/Access con datos de empleados (formato legacy)
- **Sistema B**: Sistema contable con información de salarios
- **Sistema C**: Archivos planos con histórico de contratos
- **Documentos**: Archivos físicos y digitales dispersos

### Mapping de Campos
```csv
# mapping.csv - Ejemplo para Empleados
source_system,source_table,source_column,sample_value,transform_sql,target_table,target_column,validation_rule,notes
Sistema_A,EMP_MASTER,EMP_ID,12345,CAST(EMP_ID AS INT),Empleados,IDEmpleado,NOT NULL,PK autonumérica nueva
Sistema_A,EMP_MASTER,CI_NUMBER,1234567,TRIM(CI_NUMBER),Empleados,CI,LENGTH >= 7,Validar formato CI boliviano
Sistema_A,EMP_MASTER,FULL_NAME,"Juan Pérez",PARSENAME(REPLACE(FULL_NAME,' ','.'),2),Empleados,Nombres,NOT NULL,Split por espacios
Sistema_A,EMP_MASTER,BIRTH_DT,19850315,CONVERT(DATE,BIRTH_DT),Empleados,FechaNacimiento,AGE BETWEEN 18 AND 70,Formato YYYYMMDD
```

## Estrategia de Migración

### Fases de Migración
1. **Fase 0**: Análisis y validación de datos fuente
2. **Fase 1**: Migración de datos maestros (Departamentos, Cargos)
3. **Fase 2**: Migración de Empleados
4. **Fase 3**: Migración de Contratos históricos
5. **Fase 4**: Migración de Subsidios y Anticipos
6. **Fase 5**: Validación final y puesta en producción

### Scripts de Transformación

#### Script Base para Empleados
```sql
-- ETL_Empleados.sql
CREATE PROCEDURE sp_MigrarEmpleados
    @LoteId INT,
    @TamañoLote INT = 100
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        BEGIN TRANSACTION;
        
        -- Log inicio de lote
        INSERT INTO MigrationLog (Proceso, LoteId, Estado, FechaInicio)
        VALUES ('Empleados', @LoteId, 'Iniciado', GETDATE());
        
        -- Crear tabla temporal con datos transformados
        CREATE TABLE #EmpleadosTemp (
            CI NVARCHAR(15),
            Nombres NVARCHAR(100),
            ApellidoPaterno NVARCHAR(50),
            ApellidoMaterno NVARCHAR(50),
            FechaNacimiento DATE,
            Email NVARCHAR(150),
            ValidationHash NVARCHAR(64)
        );
        
        -- Insertar datos transformados con validaciones
        INSERT INTO #EmpleadosTemp
        SELECT 
            TRIM(src.CI_NUMBER) AS CI,
            TRIM(PARSENAME(REPLACE(src.FULL_NAME, ' ', '.'), 3)) AS Nombres,
            TRIM(PARSENAME(REPLACE(src.FULL_NAME, ' ', '.'), 2)) AS ApellidoPaterno,
            TRIM(PARSENAME(REPLACE(src.FULL_NAME, ' ', '.'), 1)) AS ApellidoMaterno,
            CONVERT(DATE, src.BIRTH_DT) AS FechaNacimiento,
            LOWER(TRIM(src.EMAIL_ADDR)) AS Email,
            HASHBYTES('SHA256', CONCAT(src.CI_NUMBER, '|', src.FULL_NAME, '|', src.BIRTH_DT)) AS ValidationHash
        FROM staging.EmpleadosRaw src
        WHERE src.LoteId = @LoteId
        AND LEN(TRIM(src.CI_NUMBER)) >= 7
        AND src.BIRTH_DT IS NOT NULL
        AND DATEDIFF(YEAR, CONVERT(DATE, src.BIRTH_DT), GETDATE()) BETWEEN 18 AND 70;
        
        -- Verificar duplicados
        IF EXISTS (
            SELECT CI FROM #EmpleadosTemp 
            GROUP BY CI HAVING COUNT(*) > 1
        )
        BEGIN
            THROW 50001, 'Duplicados detectados en lote', 1;
        END
        
        -- Insertar en tabla final
        INSERT INTO Empleados (CI, Nombres, ApellidoPaterno, ApellidoMaterno, FechaNacimiento, Email, Estado, UsuarioCreacion)
        SELECT CI, Nombres, ApellidoPaterno, ApellidoMaterno, FechaNacimiento, Email, 1, 'MIGRATION'
        FROM #EmpleadosTemp;
        
        -- Log finalización exitosa
        DECLARE @RegistrosProcesados INT = @@ROWCOUNT;
        UPDATE MigrationLog 
        SET Estado = 'Completado', 
            FechaFin = GETDATE(),
            RegistrosProcesados = @RegistrosProcesados
        WHERE Proceso = 'Empleados' AND LoteId = @LoteId;
        
        COMMIT TRANSACTION;
        
    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 ROLLBACK TRANSACTION;
        
        UPDATE MigrationLog 
        SET Estado = 'Error',
            FechaFin = GETDATE(),
            ErrorMessage = ERROR_MESSAGE()
        WHERE Proceso = 'Empleados' AND LoteId = @LoteId;
        
        THROW;
    END CATCH
END
```

## Validación y Reconciliación

### Scripts de Reconciliación Automatizada
```sql
-- Reconciliación de conteos por entidad
CREATE PROCEDURE sp_ValidarMigracion
AS
BEGIN
    DECLARE @ResultadoValidacion TABLE (
        Entidad NVARCHAR(50),
        CountOrigen INT,
        CountDestino INT,
        Diferencia INT,
        Estado NVARCHAR(20)
    );
    
    -- Empleados
    INSERT INTO @ResultadoValidacion
    SELECT 'Empleados',
           (SELECT COUNT(*) FROM staging.EmpleadosRaw WHERE Estado = 'Activo'),
           (SELECT COUNT(*) FROM Empleados WHERE Estado = 1),
           0, 'Pendiente';
    
    -- Actualizar diferencias y estado
    UPDATE @ResultadoValidacion 
    SET Diferencia = CountDestino - CountOrigen,
        Estado = CASE WHEN CountDestino = CountOrigen THEN 'OK' ELSE 'ERROR' END;
    
    -- Mostrar resultados
    SELECT * FROM @ResultadoValidacion;
    
    -- Validación de checksums por muestra
    SELECT 'Checksum Validation' AS Test,
           CASE WHEN EXISTS (
               SELECT 1 FROM (
                   SELECT TOP 100 CI, HASHBYTES('SHA256', CONCAT(CI, Nombres, FechaNacimiento)) AS Hash
                   FROM Empleados ORDER BY IDEmpleado
               ) dest
               INNER JOIN (
                   SELECT CI_NUMBER AS CI, HASHBYTES('SHA256', CONCAT(CI_NUMBER, FULL_NAME, BIRTH_DT)) AS Hash
                   FROM staging.EmpleadosRaw WHERE LEN(CI_NUMBER) >= 7
               ) src ON dest.CI = src.CI AND dest.Hash = src.Hash
           ) THEN 'PASS' ELSE 'FAIL' END AS Resultado;
END
```

### Validaciones de Integridad
```sql
-- Validar relaciones FK después de migración
SELECT 'Contratos Huérfanos' AS Validation,
       COUNT(*) AS Count
FROM Contratos c
LEFT JOIN Empleados e ON c.IDEmpleado = e.IDEmpleado
WHERE e.IDEmpleado IS NULL;

-- Validar rangos de fechas
SELECT 'Fechas Inválidas' AS Validation,
       COUNT(*) AS Count
FROM Empleados 
WHERE FechaNacimiento > GETDATE() 
   OR FechaNacimiento < '1940-01-01';
```

## Plan de Rollback

### Tabla de Control de Migración
```sql
CREATE TABLE MigrationLog (
    LogId INT IDENTITY(1,1) PRIMARY KEY,
    Proceso NVARCHAR(50) NOT NULL,
    LoteId INT NOT NULL,
    Estado NVARCHAR(20) NOT NULL, -- Iniciado, Completado, Error, Rollback
    FechaInicio DATETIME2 NOT NULL,
    FechaFin DATETIME2 NULL,
    RegistrosProcesados INT NULL,
    ErrorMessage NVARCHAR(MAX) NULL,
    ChecksumInicial NVARCHAR(64) NULL,
    ChecksumFinal NVARCHAR(64) NULL
);
```

### Procedimiento de Rollback
```sql
CREATE PROCEDURE sp_RollbackMigracion
    @Proceso NVARCHAR(50),
    @LoteId INT
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        BEGIN TRANSACTION;
        
        -- Backup antes de rollback
        DECLARE @BackupName NVARCHAR(200) = CONCAT('Pre_Rollback_', @Proceso, '_', @LoteId, '_', FORMAT(GETDATE(), 'yyyyMMdd_HHmmss'));
        
        -- Eliminar registros del lote específico
        CASE @Proceso
            WHEN 'Empleados' THEN
                DELETE FROM Empleados WHERE UsuarioCreacion = 'MIGRATION' 
                AND IDEmpleado IN (SELECT EntityId FROM MigrationLog WHERE Proceso = @Proceso AND LoteId = @LoteId);
            -- Agregar otros casos según entidad
        END
        
        -- Marcar como rollback
        UPDATE MigrationLog 
        SET Estado = 'Rollback', FechaFin = GETDATE()
        WHERE Proceso = @Proceso AND LoteId = @LoteId;
        
        COMMIT TRANSACTION;
        
    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END
```

## Ejecución por Lotes

### Tamaño de Lotes Óptimo
- **Empleados**: 500 registros por lote (estimado 2-3 minutos)
- **Contratos**: 200 registros por lote (validaciones FK complejas)
- **Histórico**: 1000 registros por lote (menos validaciones)

### Script de Coordinación
```bash
#!/bin/bash
# migrate-coordinator.sh

PROCESO="Empleados"
TOTAL_LOTES=10

for i in $(seq 1 $TOTAL_LOTES); do
    echo "Ejecutando lote $i de $TOTAL_LOTES"
    
    sqlcmd -S localhost -d RRHH_DB -Q "EXEC sp_MigrarEmpleados @LoteId = $i"
    
    if [ $? -ne 0 ]; then
        echo "Error en lote $i. Ejecutando rollback..."
        sqlcmd -S localhost -d RRHH_DB -Q "EXEC sp_RollbackMigracion @Proceso = '$PROCESO', @LoteId = $i"
        exit 1
    fi
    
    echo "Lote $i completado exitosamente"
    sleep 5 # Pausa entre lotes
done

echo "Migración completada. Ejecutando validación final..."
sqlcmd -S localhost -d RRHH_DB -Q "EXEC sp_ValidarMigracion"
```

## Testing de Migración

### Datos de Prueba
- **Dataset Mínimo**: 50 empleados con casos edge
- **Dataset Completo**: 335 empleados reales enmascarados
- **Casos Edge**: CIs duplicados, fechas inválidas, nombres con caracteres especiales

### Validaciones Automatizadas
- **Count Validation**: Totales por entidad coinciden
- **Checksum Validation**: Hashes de registros muestreados coinciden
- **Business Rules**: Todas las validaciones de negocio pasan
- **Performance**: Migración completa < 2 horas

## Dependencias
- Acceso de lectura a sistemas fuente
- Entorno staging con esquema destino implementado
- SQL Server con permisos para bulk operations
- Storage temporal para archivos de respaldo
- Herramientas de validación y testing

## Criterios de Aceptación
- [ ] Mapping completo documentado y validado
- [ ] Scripts de migración probados en staging
- [ ] Validaciones automatizadas implementadas
- [ ] Procedimientos de rollback probados
- [ ] Performance dentro de ventanas aceptables
- [ ] Reconciliación 100% exitosa en datos críticos
- [ ] Zero data loss validado con checksums
- [ ] Documentación operativa completa
- [ ] Training del equipo en procedimientos
- [ ] Contingency plan aprobado por stakeholders

## Referencias al Documento Canónico
Este documento se basa en las secciones 23.1, 48 y 56 del [Project Chapter](../projectChapter.md). Para scripts específicos, ejemplos de validación y guías operativas detalladas, consultar el documento principal.

**Supuestos:**
- Datos fuente en formatos estándar (CSV, Excel, SQL exports)
- Ventana de mantenimiento de 4-6 horas disponible
- Equipo técnico disponible durante migración para troubleshooting