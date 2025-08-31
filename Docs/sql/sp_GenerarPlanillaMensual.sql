-- sp_GenerarPlanillaMensual (esqueleto)
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
    IF @Mes NOT BETWEEN 1 AND 12
    BEGIN
        THROW 50001, 'Parametro @Mes invalido', 1;
    END

    -- Idempotencia: si existe IdempotencyKey devolver el PlanillaId existente
    IF @IdempotencyKey IS NOT NULL
    BEGIN
        IF EXISTS (SELECT 1 FROM LogPlanilla WHERE IdempotencyKey = @IdempotencyKey)
        BEGIN
            SELECT @PlanillaId = PlanillaId FROM LogPlanilla WHERE IdempotencyKey = @IdempotencyKey;
            RETURN;
        END
    END

    -- Registrar inicio en LogPlanilla
    INSERT INTO LogPlanilla (IdempotencyKey, Mes, Gestion, Usuario, FechaInicio, EstadoProceso)
    VALUES (@IdempotencyKey, @Mes, @Gestion, @Usuario, SYSUTCDATETIME(), 'Iniciado');

    SELECT @PlanillaId = SCOPE_IDENTITY();

    BEGIN TRY
        -- Intentar adquirir applock exclusivo para el periodo
        DECLARE @rc INT;
        DECLARE @resource NVARCHAR(200) = CONCAT('planilla_', @Gestion, '_', RIGHT('00' + CAST(@Mes AS VARCHAR(2)),2));
        EXEC @rc = sp_getapplock @Resource = @resource, @LockMode = 'Exclusive', @LockTimeout = 30000;
        IF @rc < 0
        BEGIN
            UPDATE LogPlanilla SET EstadoProceso = 'Error', Observaciones = 'No se pudo adquirir bloqueo' WHERE PlanillaId = @PlanillaId;
            THROW 51000, 'No se pudo adquirir bloqueo para el periodo', 1;
        END

        -- Snapshot de contratos activos (tabla temporal)
        CREATE TABLE #ContratosActivos (IDContrato INT PRIMARY KEY, IDEmpleado INT, HaberBasico DECIMAL(18,2));
        INSERT INTO #ContratosActivos (IDContrato, IDEmpleado, HaberBasico)
        SELECT c.IDContrato, c.IDEmpleado, c.HaberBasico
        FROM Contratos c
        WHERE c.Estado = 1; -- filtrado ejemplo

        -- Procesar en batches: llamar a sp_CalcularSalarioMensual por contrato o usar lógica set-based
        -- EJEMPLO simple: cursor/batch (reemplazar por lógica set-based para rendimiento)

        DECLARE @IDContrato INT;
        DECLARE cur CURSOR LOCAL FAST_FORWARD FOR SELECT IDContrato FROM #ContratosActivos;
        OPEN cur;
        FETCH NEXT FROM cur INTO @IDContrato;
        WHILE @@FETCH_STATUS = 0
        BEGIN
            -- Llamada al SP por contrato (puede ser reemplazada por lógica set-based)
            EXEC sp_CalcularSalarioMensual @IDContrato = @IDContrato, @Mes = @Mes, @Gestion = @Gestion;
            FETCH NEXT FROM cur INTO @IDContrato;
        END
        CLOSE cur;
        DEALLOCATE cur;

        -- Marcar anticipos como descontados (ejemplo)
        -- UPDATE Anticipos SET Estado = 'Descontado' WHERE ...;

        -- Actualizar LogPlanilla como Completado
        UPDATE LogPlanilla SET EstadoProceso = 'Completado', FechaFin = SYSUTCDATETIME(), ContratosProcessados = (SELECT COUNT(1) FROM #ContratosActivos) WHERE PlanillaId = @PlanillaId;

        -- Liberar applock
        EXEC sp_releaseapplock @Resource = @resource;

    END TRY
    BEGIN CATCH
        DECLARE @err NVARCHAR(4000) = ERROR_MESSAGE();
        UPDATE LogPlanilla SET EstadoProceso = 'Error', Observaciones = @err, FechaFin = SYSUTCDATETIME() WHERE PlanillaId = @PlanillaId;
        -- Asegurar liberación de lock si aplica
        BEGIN TRY EXEC sp_releaseapplock @Resource = @resource; END TRY CATCH  END CATCH;
        THROW;
    END CATCH
END
GO
