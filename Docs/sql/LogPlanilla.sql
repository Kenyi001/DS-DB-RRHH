-- DDL: LogPlanilla
CREATE TABLE LogPlanilla (
    PlanillaId INT IDENTITY(1,1) PRIMARY KEY,
    IdempotencyKey UNIQUEIDENTIFIER NULL,
    Mes INT NOT NULL,
    Gestion INT NOT NULL,
    Usuario NVARCHAR(100) NOT NULL,
    FechaInicio DATETIME2 NOT NULL,
    FechaFin DATETIME2 NULL,
    EstadoProceso NVARCHAR(20) NOT NULL,
    ContratosProcessados INT NULL,
    Observaciones NVARCHAR(500) NULL,
    UNIQUE(IdempotencyKey)
);

-- Índices útiles
CREATE NONCLUSTERED INDEX IX_LogPlanilla_Period ON LogPlanilla (Gestion, Mes);
