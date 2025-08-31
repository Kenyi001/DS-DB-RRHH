# Especificación de Modelo de Base de Datos - Sistema RRHH YPFB-Andina

## Propósito
Documentar el modelo de datos completo del Sistema RRHH, incluyendo tablas principales, relaciones, índices y objetos SQL críticos para garantizar integridad, performance y auditoría.

## Alcance
- Modelo relacional normalizado para 335+ empleados
- Tablas principales y de auditoría
- Índices optimizados para consultas frecuentes
- Objetos SQL: Stored Procedures, triggers, funciones
- Estrategias de concurrencia y auditoría

## Tablas Principales

### Entidades Core
- **Empleados**: IDEmpleado (PK), CI (UK), Nombres, ApellidoPaterno, ApellidoMaterno, FechaNacimiento, Email, Telefono, Estado, rowversion
- **Contratos**: IDContrato (PK), IDEmpleado (FK), NumeroContrato (UK), FechaInicio, FechaFin, HaberBasico, IDDepartamento (FK), IDCargo (FK), Estado, rowversion
- **GestionSalarios**: IDGestion (PK), IDContrato (FK), Mes, Gestion, HaberBasico, TotalSubsidios, TotalDescuentos, LiquidoPagable, FechaPago, Estado

### Módulos Específicos
- **Subsidios**: IDSubsidio (PK), IDContrato (FK), IDTipoSubsidio (FK), Monto, FechaInicio, FechaFin, Estado
- **TiposSubsidio**: IDTipoSubsidio (PK), Nombre, Descripcion, EsPorcentaje, MontoFijo
- **Anticipos**: IDAnticipo (PK), IDContrato (FK), MontoAnticipo, FechaSolicitud, FechaAprobacion, EstadoAnticipo, MesDescuento
- **SolicitudesVacaciones**: IDSolicitud (PK), IDContrato (FK), FechaInicio, FechaFin, DiasVacaciones, JefeAprobador, EstadoSolicitud

### Tablas de Soporte
- **Departamentos**: IDDepartamento (PK), Nombre, Descripcion, Estado
- **Cargos**: IDCargo (PK), Nombre, Descripcion, Estado
- **Documentos**: IDDocumento (PK), NombreArchivo, RutaStorage, TipoMIME, Tamaño, IDEntidad, TipoEntidad
- **AuditLog**: AuditId (PK), Entity, EntityId, Action, UserId, CreatedAt, PayloadBefore, PayloadAfter, TraceId, IP

## Índices Optimizados

### Índices de Performance Crítica
```sql
-- Planilla y salarios
CREATE NONCLUSTERED INDEX IX_GestionSalarios_PagosPendientes
ON GestionSalarios (Gestion, Mes)
INCLUDE (IDContrato, LiquidoPagable, FechaPago);

-- Contratos activos por departamento
CREATE NONCLUSTERED INDEX IX_Contratos_ActivoCargoDepto
ON Contratos (IDDepartamento, IDCargo, Estado)
INCLUDE (IDEmpleado, NumeroContrato, FechaInicio, FechaFin)
WHERE Estado = 1;

-- Vacaciones pendientes por jefe
CREATE NONCLUSTERED INDEX IX_SolicitudesVacaciones_JefePendientes
ON SolicitudesVacaciones (JefeAprobador, EstadoSolicitud, FechaInicio)
INCLUDE (IDContrato, FechaFin, DiasVacaciones);
```

## Objetos SQL Críticos

### Stored Procedures
- **sp_GenerarPlanillaMensual**: Proceso principal para calcular planilla mensual
- **sp_CalcularSalarioMensual**: Cálculo individual por contrato
- **sp_ReporteEvaluacionAnual**: Generación de reportes anuales

### Triggers de Validación
- **trg_Validar_Anticipo** (INSTEAD OF): Valida tope 50% haber básico
- **TRG_Subsidios_A_GestionSalarios** (AFTER): Propaga cambios a gestión salarios
- **trg_Empleados_Audit** (AFTER): Registra cambios en AuditLog

### Funciones de Cálculo
- **fn_CalcularSalarioTotal**: Cálculo total por empleado y período
- **fn_ValidarSolapeContrato**: Validación de contratos superpuestos
- **fn_CalcularSaldoVacaciones**: Cálculo de días disponibles

## Estrategias de Concurrencia

### Configuración de Base de Datos
```sql
-- Habilitar snapshot isolation para reducir bloqueos
ALTER DATABASE [RRHH_DB] SET READ_COMMITTED_SNAPSHOT ON;
ALTER DATABASE [RRHH_DB] SET ALLOW_SNAPSHOT_ISOLATION ON;
```

### Patrones de Concurrencia
- **Optimista**: rowversion en tablas críticas + If-Match headers
- **Application Locks**: sp_getapplock para procesos exclusivos
- **Transacciones**: BEGIN TRY/CATCH con manejo robusto de errores

## Auditoría y Trazabilidad

### Tabla AuditLog (Estructura)
```sql
CREATE TABLE AuditLog (
    AuditId BIGINT IDENTITY(1,1) PRIMARY KEY,
    Entity NVARCHAR(50) NOT NULL,
    EntityId INT NOT NULL,
    Action NVARCHAR(20) NOT NULL,
    UserId INT NULL,
    TraceId UNIQUEIDENTIFIER NULL,
    CreatedAt DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME(),
    PayloadBefore NVARCHAR(MAX) NULL,
    PayloadAfter NVARCHAR(MAX) NULL,
    IP NVARCHAR(45) NULL,
    Comments NVARCHAR(500) NULL
);
```

### LogPlanilla (Tracking de Procesos)
```sql
CREATE TABLE LogPlanilla (
    PlanillaId INT IDENTITY(1,1) PRIMARY KEY,
    IdempotencyKey UNIQUEIDENTIFIER NULL UNIQUE,
    Mes INT NOT NULL,
    Gestion INT NOT NULL,
    Usuario NVARCHAR(100) NOT NULL,
    FechaInicio DATETIME2 NOT NULL,
    FechaFin DATETIME2 NULL,
    EstadoProceso NVARCHAR(20) NOT NULL,
    ContratosProcessados INT NULL,
    Observaciones NVARCHAR(500) NULL
);
```

## Dependencias
- SQL Server 2019/2022 con permisos para crear objetos
- Laravel 11 con drivers pdo_sqlsrv y sqlsrv habilitados
- Redis para cache de queries y rate limiting
- Sistema de archivos S3-compatible para documentos

## Criterios de Aceptación
- [ ] Modelo normalizado hasta 3FN implementado
- [ ] Índices críticos creados y optimizados
- [ ] Triggers de validación funcionando correctamente
- [ ] Stored Procedures con manejo de errores robusto
- [ ] Auditoría completa en operaciones críticas
- [ ] Tests de integridad referencial pasando
- [ ] Performance de queries críticas < 2 segundos
- [ ] Backup y restore procedures verificados
- [ ] Concurrencia optimista/pesimista implementada según caso
- [ ] Migrations idempotentes para todos los objetos

## Referencias al Documento Canónico
Este documento se basa en las secciones 5, 6, 7, 19 y 20 del [Project Chapter](../projectChapter.md). Para scripts SQL completos, ejemplos de triggers y detalles de implementación, consultar el documento principal.

**Supuestos:**
- Base de datos dedicada para Sistema RRHH
- Usuario de aplicación con permisos DML limitados
- Backups automatizados configurados cada 15 minutos