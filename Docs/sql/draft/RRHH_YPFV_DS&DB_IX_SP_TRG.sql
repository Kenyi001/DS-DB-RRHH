/* =====================================================================
   RRHH – INSTALADOR DE OBJETOS (Índices, SPs, Triggers, Funciones)
   SQL Server 2016 SP1+ (2017/2019/2022 OK)
   ===================================================================== */

------------------------------------------------------------
-- 1) CREAR BD SI NO EXISTE Y USARLA
------------------------------------------------------------
IF DB_ID(N'YPFB_RRHH') IS NULL
BEGIN
    PRINT N'Creando base de datos [YPFB_RRHH]...';
    EXEC(N'CREATE DATABASE [YPFB_RRHH];');
END
ELSE
BEGIN
    PRINT N'La base de datos [YPFB_RRHH] ya existe.';
END
GO
USE [YPFB_RRHH];
GO

------------------------------------------------------------
-- 2) PREÁMBULO DE SESIÓN
------------------------------------------------------------
SET NOCOUNT ON;
SET XACT_ABORT ON;
GO

------------------------------------------------------------
-- 3) PREFLIGHT: TABLAS REQUERIDAS
--    (No incluimos Postulaciones porque no está en tu esquema)
------------------------------------------------------------
DECLARE @AbortIfMissingTables bit = 1;  -- 1 = aborta si faltan tablas

DECLARE @req TABLE (t sysname PRIMARY KEY);
INSERT INTO @req(t) VALUES
 (N'GestionSalarios'),(N'Contratos'),(N'Subsidios'),(N'Anticipos'),(N'Finiquitos'),
 (N'SolicitudesVacaciones'),(N'Empleados'),(N'Evaluaciones'),
 (N'Categorias'),(N'Cargos'),(N'Departamentos'),(N'TiposSubsidio'),
 (N'AjustesSalariales'),(N'EntregasLactancia'),(N'SolicitudesLactancia');

;WITH faltantes AS (
    SELECT r.t
    FROM @req r
    WHERE NOT EXISTS (SELECT 1 FROM sys.tables WHERE name = r.t AND schema_id = SCHEMA_ID('dbo'))
)
SELECT t AS TablaFaltante
INTO #TablasFaltantes
FROM faltantes;

IF EXISTS (SELECT 1 FROM #TablasFaltantes)
BEGIN
    PRINT N'*** ATENCIÓN: Faltan tablas requeridas:';
    SELECT * FROM #TablasFaltantes;

    IF (@AbortIfMissingTables = 1)
    BEGIN
        DROP TABLE #TablasFaltantes;
        THROW 51000, N'Preflight falló: faltan tablas requeridas. Crea las tablas y reintenta.', 1;
    END
    ELSE
    BEGIN
        PRINT N'Continuando a pedido (AbortIfMissingTables=0). Podrán fallar objetos dependientes.';
    END
END
ELSE
BEGIN
    PRINT N'Preflight OK: Todas las tablas requeridas existen.';
    DROP TABLE #TablasFaltantes;
END
GO

/* =====================================================================
   4) ÍNDICES (con guards para no romper en tablas inexistentes)
   ===================================================================== */
-- GestionSalarios
IF OBJECT_ID(N'dbo.GestionSalarios','U') IS NOT NULL
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM sys.indexes 
        WHERE name = N'IX_GestionSalarios_PagosPendientes'
          AND object_id = OBJECT_ID(N'dbo.GestionSalarios')
    )
    BEGIN
        CREATE NONCLUSTERED INDEX IX_GestionSalarios_PagosPendientes
            ON dbo.GestionSalarios (Gestion, Mes)
            INCLUDE (IDContrato, LiquidoPagable, FechaPago);
        PRINT N'Creado: IX_GestionSalarios_PagosPendientes';
    END
    ELSE PRINT N'Existe: IX_GestionSalarios_PagosPendientes';
END

-- Contratos (filtrado Estado=1)
IF OBJECT_ID(N'dbo.Contratos','U') IS NOT NULL
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM sys.indexes 
        WHERE name = N'IX_Contratos_ActivoCargoDepto'
          AND object_id = OBJECT_ID(N'dbo.Contratos')
    )
    BEGIN
        CREATE NONCLUSTERED INDEX IX_Contratos_ActivoCargoDepto
            ON dbo.Contratos (IDDepartamento, IDCargo, Estado)
            INCLUDE (IDEmpleado, NumeroContrato, FechaInicio, FechaFin)
            WHERE Estado = 1;
        PRINT N'Creado: IX_Contratos_ActivoCargoDepto';
    END
    ELSE PRINT N'Existe: IX_Contratos_ActivoCargoDepto';
END

-- SolicitudesVacaciones
IF OBJECT_ID(N'dbo.SolicitudesVacaciones','U') IS NOT NULL
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM sys.indexes 
        WHERE name = N'IX_SolicitudesVacaciones_JefePendientes'
          AND object_id = OBJECT_ID(N'dbo.SolicitudesVacaciones')
    )
    BEGIN
        CREATE NONCLUSTERED INDEX IX_SolicitudesVacaciones_JefePendientes
            ON dbo.SolicitudesVacaciones (JefeAprobador, EstadoSolicitud, FechaInicio)
            INCLUDE (IDContrato, FechaFin, DiasVacaciones, Observaciones);
        PRINT N'Creado: IX_SolicitudesVacaciones_JefePendientes';
    END
    ELSE PRINT N'Existe: IX_SolicitudesVacaciones_JefePendientes';
END

-- Empleados
IF OBJECT_ID(N'dbo.Empleados','U') IS NOT NULL
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM sys.indexes 
        WHERE name = N'IX_Empleados_Departamento_Estado'
          AND object_id = OBJECT_ID(N'dbo.Empleados')
    )
    BEGIN
        CREATE NONCLUSTERED INDEX IX_Empleados_Departamento_Estado
            ON dbo.Empleados (Estado)
            INCLUDE (Nombres, ApellidoPaterno);
        PRINT N'Creado: IX_Empleados_Departamento_Estado';
    END
    ELSE PRINT N'Existe: IX_Empleados_Departamento_Estado';
END

-- Postulaciones (OPCIONAL: solo si existe la tabla en tu esquema)
IF OBJECT_ID(N'dbo.Postulaciones','U') IS NOT NULL
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM sys.indexes 
        WHERE name = N'IX_Postulaciones_Recientes'
          AND object_id = OBJECT_ID(N'dbo.Postulaciones')
    )
    BEGIN
        EXEC(N'
            CREATE NONCLUSTERED INDEX IX_Postulaciones_Recientes
            ON dbo.Postulaciones (FechaPostulacion DESC)
            WHERE Estado = N''Pendiente'';
        ');
        PRINT N'Creado: IX_Postulaciones_Recientes';
    END
    ELSE PRINT N'Existe: IX_Postulaciones_Recientes';
END
ELSE
BEGIN
    PRINT N'Saltado: dbo.Postulaciones no existe en este esquema.';
END
GO

/* =====================================================================
   5) PROCEDURES
   ===================================================================== */
SET ANSI_NULLS ON;
SET QUOTED_IDENTIFIER ON;
GO

-- sp_CalcularSalarioMensual
CREATE OR ALTER PROCEDURE dbo.sp_CalcularSalarioMensual
    @IDContrato INT,
    @Mes INT,
    @Gestion INT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @Basico DECIMAL(10,2), 
            @TotalSubsidios DECIMAL(10,2), 
            @TotalAnticipos DECIMAL(10,2), 
            @Liquido DECIMAL(10,2);

    SELECT @Basico = c.HaberBasico
    FROM dbo.Contratos c
    WHERE c.IDContrato = @IDContrato
      AND c.Estado = 1;

    SELECT @TotalSubsidios = ISNULL(SUM(s.MontoSubsidio), 0)
    FROM dbo.Subsidios s
    WHERE s.IDContrato = @IDContrato
      AND s.Estado = 1
      AND s.FechaInicio <= EOMONTH(DATEFROMPARTS(@Gestion, @Mes, 1))
      AND (s.FechaFin IS NULL OR s.FechaFin >= DATEFROMPARTS(@Gestion, @Mes, 1));

    SELECT @TotalAnticipos = ISNULL(SUM(a.MontoAnticipo), 0)
    FROM dbo.Anticipos a
    WHERE a.IDContrato = @IDContrato
      AND UPPER(a.EstadoAnticipo) = 'PENDIENTE'
      AND a.MesDescuento = @Mes
      AND a.GestionDescuento = @Gestion;

    SET @Liquido = ISNULL(@Basico,0) + ISNULL(@TotalSubsidios,0) - ISNULL(@TotalAnticipos,0);

    IF EXISTS (SELECT 1 FROM dbo.GestionSalarios 
               WHERE IDContrato = @IDContrato 
                 AND Mes = @Mes 
                 AND Gestion = @Gestion)
    BEGIN
        UPDATE dbo.GestionSalarios
        SET SalarioBasico   = @Basico,
            TotalIngresos   = ISNULL(@Basico,0) + ISNULL(@TotalSubsidios,0),
            TotalDescuentos = ISNULL(@TotalAnticipos,0),
            LiquidoPagable  = @Liquido,
            FechaPago       = NULL,
            EstadoPago      = N'Pendiente'
        WHERE IDContrato = @IDContrato 
          AND Mes = @Mes 
          AND Gestion = @Gestion;
    END
    ELSE
    BEGIN
        INSERT INTO dbo.GestionSalarios 
            (IDContrato, Mes, Gestion, SalarioBasico, TotalIngresos, TotalDescuentos, LiquidoPagable, FechaPago, EstadoPago)
        VALUES 
            (@IDContrato, @Mes, @Gestion, @Basico, ISNULL(@Basico,0) + ISNULL(@TotalSubsidios,0), ISNULL(@TotalAnticipos,0), @Liquido, NULL, N'Pendiente');
    END
END
GO

-- sp_ReporteEvaluacionAnual
CREATE OR ALTER PROCEDURE dbo.sp_ReporteEvaluacionAnual
    @Gestion INT
AS
BEGIN
    SET NOCOUNT ON;

    ;WITH Promedios AS (
        SELECT 
            E.IDEmpleado,
            AVG(Ev.Calificacion * 1.0) AS PromedioCalificacion,
            COUNT(Ev.IDEvaluacion)     AS CantidadEvaluaciones,
            MAX(Ev.FechaEvaluacion)    AS UltimaEvaluacion
        FROM dbo.Empleados E
        INNER JOIN dbo.Evaluaciones Ev ON E.IDEmpleado = Ev.IDEmpleado
        WHERE Ev.Gestion = @Gestion
        GROUP BY E.IDEmpleado
    )
    SELECT 
        E.IDEmpleado,
        E.Nombres + N' ' + E.ApellidoPaterno + ISNULL(N' ' + E.ApellidoMaterno, N'') AS NombreCompleto,
        D.NombreDepartamento,
        Cg.NombreCargo,
        Ct.NombreCategoria,
        P.PromedioCalificacion,
        P.CantidadEvaluaciones,
        P.UltimaEvaluacion,
        CASE 
            WHEN P.PromedioCalificacion >= 4.5 THEN N'Excelente'
            WHEN P.PromedioCalificacion >= 3.5 THEN N'Bueno'
            WHEN P.PromedioCalificacion >= 2.5 THEN N'Regular'
            ELSE N'Deficiente'
        END AS CategoriaDesempeno,
        DATEDIFF(YEAR, Ctt.FechaInicio, GETDATE()) AS AntiguedadAnios,
        Ctt.TipoContrato,
        Ctt.HaberBasico,
        ISNULL((
            SELECT SUM(Sb.MontoSubsidio) 
            FROM dbo.Subsidios Sb 
            WHERE Sb.IDContrato = Ctt.IDContrato 
              AND YEAR(Sb.FechaInicio) = @Gestion
        ),0) AS TotalSubsidios,
        ISNULL((
            SELECT SUM(Sv.DiasVacaciones)
            FROM dbo.SolicitudesVacaciones Sv
            WHERE Sv.IDContrato = Ctt.IDContrato 
              AND YEAR(Sv.FechaInicio) = @Gestion
              AND Sv.EstadoSolicitud = N'Tomada'
        ),0) AS DiasVacacionesTomadas,
        ISNULL((
            SELECT SUM(A.MontoAnticipo)
            FROM dbo.Anticipos A
            WHERE A.IDContrato = Ctt.IDContrato 
              AND A.GestionDescuento = @Gestion
        ),0) AS TotalAnticipos,
        ISNULL((
            SELECT SUM(Gs.LiquidoPagable)
            FROM dbo.GestionSalarios Gs
            WHERE Gs.IDContrato = Ctt.IDContrato 
              AND Gs.Gestion = @Gestion
        ),0) AS TotalLiquidoPagado,
        (SELECT TOP 1 Ev2.Evaluador
         FROM dbo.Evaluaciones Ev2
         WHERE Ev2.IDEmpleado = E.IDEmpleado 
           AND Ev2.Gestion = @Gestion
         GROUP BY Ev2.Evaluador
         ORDER BY COUNT(*) DESC) AS EvaluadorFrecuente
    FROM Promedios P
    INNER JOIN dbo.Empleados     E   ON E.IDEmpleado = P.IDEmpleado
    INNER JOIN dbo.Contratos     Ctt ON Ctt.IDEmpleado = E.IDEmpleado AND Ctt.Estado = 1
    INNER JOIN dbo.Categorias    Ct  ON Ct.IDCategoria = Ctt.IDCategoria
    INNER JOIN dbo.Cargos        Cg  ON Cg.IDCargo = Ctt.IDCargo
    INNER JOIN dbo.Departamentos D   ON D.IDDepartamento = Ctt.IDDepartamento
    ORDER BY P.PromedioCalificacion DESC;
END
GO

/* =====================================================================
   6) TRIGGERS
   ===================================================================== */
SET ANSI_NULLS ON;
SET QUOTED_IDENTIFIER ON;
GO

-- trg_Validar_Anticipo
IF OBJECT_ID('dbo.trg_Validar_Anticipo', 'TR') IS NOT NULL
    DROP TRIGGER dbo.trg_Validar_Anticipo;
GO
CREATE TRIGGER dbo.trg_Validar_Anticipo
ON dbo.Anticipos
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;

    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN dbo.Contratos c ON i.IDContrato = c.IDContrato
        WHERE i.MontoAnticipo > (c.HaberBasico * 0.5)
    )
    BEGIN
        THROW 50001, N'El anticipo no puede superar el 50% del salario básico vigente del contrato.', 1;
    END;

    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN dbo.Contratos c ON i.IDContrato = c.IDContrato
        WHERE c.Estado = 0 OR (c.FechaFin IS NOT NULL AND c.FechaFin < GETDATE())
    )
    BEGIN
        THROW 50002, N'El contrato está inactivo o finalizado. No se pueden registrar anticipos.', 1;
    END;

    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN dbo.Anticipos a ON i.IDContrato = a.IDContrato
        WHERE UPPER(a.EstadoAnticipo) = 'PENDIENTE'
    )
    BEGIN
        THROW 50003, N'Ya existe un anticipo pendiente para este contrato. Cancele o descuéntelo antes de solicitar otro.', 1;
    END;

    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN dbo.Finiquitos f ON i.IDContrato = f.IDContrato
        WHERE f.EstadoFiniquito IN (N'Pagado', N'Calculado')
    )
    BEGIN
        THROW 50004, N'El empleado tiene un finiquito asociado; no puede solicitar anticipos.', 1;
    END;

    INSERT INTO dbo.Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
    SELECT 
        i.IDContrato, 
        i.MontoAnticipo, 
        ISNULL(i.FechaAnticipo, GETDATE()),
        i.MesDescuento, 
        i.GestionDescuento, 
        i.EstadoAnticipo
    FROM inserted i;
END
GO

-- TRG_Subsidios_A_GestionSalarios
IF OBJECT_ID('dbo.TRG_Subsidios_A_GestionSalarios', 'TR') IS NOT NULL
    DROP TRIGGER dbo.TRG_Subsidios_A_GestionSalarios;
GO
CREATE TRIGGER dbo.TRG_Subsidios_A_GestionSalarios
ON dbo.Subsidios
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    ;WITH SubsidiosProcesados AS (
        SELECT 
            i.IDSubsidio,
            i.IDContrato,
            i.MontoSubsidio,
            MONTH(i.FechaInicio) AS MesSubsidio,
            YEAR(i.FechaInicio)  AS GestionSubsidio
        FROM inserted i
        INNER JOIN dbo.Contratos c ON i.IDContrato = c.IDContrato
        WHERE i.Estado = 1 
          AND c.Estado = 1
          AND (c.FechaFin IS NULL OR c.FechaFin >= i.FechaInicio)
    )
    SELECT 
        IDSubsidio, IDContrato, MontoSubsidio, MesSubsidio, GestionSubsidio
    INTO #SubsidiosTemp 
    FROM SubsidiosProcesados;

    IF EXISTS (
        SELECT 1
        FROM #SubsidiosTemp sp
        INNER JOIN dbo.Subsidios s
            ON sp.IDContrato = s.IDContrato
           AND MONTH(s.FechaInicio) = sp.MesSubsidio
           AND YEAR(s.FechaInicio)  = sp.GestionSubsidio
           AND s.IDSubsidio <> sp.IDSubsidio
           AND s.Estado = 1
    )
    BEGIN
        DROP TABLE #SubsidiosTemp;
        THROW 50005, N'Ya existe un subsidio activo para este contrato en el mismo mes y gestión.', 1;
    END;

    MERGE dbo.GestionSalarios AS gs
    USING #SubsidiosTemp sp
       ON gs.IDContrato = sp.IDContrato
      AND gs.Mes       = sp.MesSubsidio
      AND gs.Gestion   = sp.GestionSubsidio
    WHEN MATCHED THEN
        UPDATE SET 
            gs.TotalIngresos  = ISNULL(gs.TotalIngresos,0)  + sp.MontoSubsidio,
            gs.LiquidoPagable = ISNULL(gs.LiquidoPagable,0) + sp.MontoSubsidio
    WHEN NOT MATCHED THEN
        INSERT (IDContrato, Mes, Gestion, DiasTrabajos, SalarioBasico, TotalIngresos, TotalDescuentos, LiquidoPagable, FechaPago, EstadoPago)
        VALUES (
            sp.IDContrato,
            sp.MesSubsidio,
            sp.GestionSubsidio,
            30,
            (SELECT TOP 1 c.HaberBasico FROM dbo.Contratos c WHERE c.IDContrato = sp.IDContrato),
            (SELECT TOP 1 c.HaberBasico FROM dbo.Contratos c WHERE c.IDContrato = sp.IDContrato) + sp.MontoSubsidio,
            0,
            (SELECT TOP 1 c.HaberBasico FROM dbo.Contratos c WHERE c.IDContrato = sp.IDContrato) + sp.MontoSubsidio,
            NULL,
            N'Pendiente'
        );

    DROP TABLE #SubsidiosTemp;
END
GO

/* =====================================================================
   7) FUNCIONES
   ===================================================================== */
SET ANSI_NULLS ON;
SET QUOTED_IDENTIFIER ON;
GO

-- fn_CalcularSalarioTotal
CREATE OR ALTER FUNCTION dbo.fn_CalcularSalarioTotal
(
    @IDEmpleado INT,
    @FechaInicio DATE,
    @FechaFin DATE
)
RETURNS DECIMAL(12,2)
AS
BEGIN
    DECLARE @Total DECIMAL(12,2) = 0;
    DECLARE @IDContrato INT;
    DECLARE @IDCategoria INT;

    SELECT TOP 1 
        @IDContrato  = c.IDContrato,
        @IDCategoria = c.IDCategoria
    FROM dbo.Contratos c
    WHERE c.IDEmpleado = @IDEmpleado
      AND c.Estado = 1
      AND (c.FechaFin IS NULL OR c.FechaFin >= @FechaInicio)
    ORDER BY c.FechaInicio DESC;

    IF @IDContrato IS NULL
        RETURN 0;

    SELECT @Total = ISNULL(SUM(gs.LiquidoPagable),0)
    FROM dbo.GestionSalarios gs
    WHERE gs.IDContrato = @IDContrato
      AND gs.FechaPago BETWEEN @FechaInicio AND @FechaFin
      AND UPPER(gs.EstadoPago) = 'PAGADO';

    SELECT @Total = @Total + ISNULL(SUM(a.HaberBasico),0)
    FROM dbo.AjustesSalariales a
    WHERE a.IDCategoria = @IDCategoria
      AND a.FechaVigencia <= @FechaFin
      AND (a.FechaFinVigencia IS NULL OR a.FechaFinVigencia >= @FechaInicio)
      AND a.Estado = 1;

    SELECT @Total = @Total + ISNULL(SUM(s.MontoSubsidio),0)
    FROM dbo.Subsidios s
    WHERE s.IDContrato = @IDContrato
      AND s.FechaInicio <= @FechaFin
      AND (s.FechaFin IS NULL OR s.FechaFin >= @FechaInicio)
      AND s.Estado = 1;

    SELECT @Total = @Total - ISNULL(SUM(a.MontoAnticipo),0)
    FROM dbo.Anticipos a
    WHERE a.IDContrato = @IDContrato
      AND a.FechaAnticipo BETWEEN @FechaInicio AND @FechaFin
      AND UPPER(a.EstadoAnticipo) = 'DESCONTADO';

    RETURN @Total;
END
GO

-- fn_TotalBeneficiosPeriodo (TVF)
CREATE OR ALTER FUNCTION dbo.fn_TotalBeneficiosPeriodo
(
    @IDEmpleado INT,
    @FechaInicio DATE,
    @FechaFin DATE
)
RETURNS TABLE
AS
RETURN
(
    SELECT
        c.IDContrato,
        e.Nombres + N' ' + e.ApellidoPaterno + N' ' + ISNULL(e.ApellidoMaterno, N'') AS Empleado,
        SUM(gs.LiquidoPagable) AS TotalSalarios,
        SUM(sub.MontoSubsidio) AS TotalSubsidios,
        SUM(CASE WHEN sv.EstadoSolicitud = N'Tomada' 
                 THEN ISNULL(gs.SalarioBasico,0) * sv.DiasVacaciones / 30.0 
                 ELSE 0 END) AS BonificacionVacaciones,
        SUM(el.MontoEntregado) AS TotalLactancia,
        SUM(f.LiquidoFiniquito) AS TotalFiniquito
    FROM dbo.Empleados e
    INNER JOIN dbo.Contratos c 
        ON e.IDEmpleado = c.IDEmpleado
    LEFT JOIN dbo.GestionSalarios gs 
        ON c.IDContrato = gs.IDContrato 
       AND gs.FechaPago BETWEEN @FechaInicio AND @FechaFin
    LEFT JOIN dbo.Subsidios sub 
        ON c.IDContrato = sub.IDContrato 
       AND sub.FechaInicio <= @FechaFin
    LEFT JOIN dbo.SolicitudesVacaciones sv 
        ON c.IDContrato = sv.IDContrato 
       AND sv.FechaInicio BETWEEN @FechaInicio AND @FechaFin
    LEFT JOIN dbo.EntregasLactancia el
        ON EXISTS (
            SELECT 1 
            FROM dbo.SolicitudesLactancia sl 
            WHERE sl.IDSolicitudLactancia = el.IDSolicitudLactancia
              AND sl.IDContrato = c.IDContrato
        )
    LEFT JOIN dbo.Finiquitos f 
        ON c.IDContrato = f.IDContrato 
       AND f.FechaRetiro BETWEEN @FechaInicio AND @FechaFin
    GROUP BY c.IDContrato, e.Nombres, e.ApellidoPaterno, e.ApellidoMaterno
);
GO

/* =====================================================================
   8) EJEMPLOS (toggle dentro del batch)
   ===================================================================== */
DECLARE @RUN_EXAMPLES bit = 0;  -- cambia a 1 para ejecutar ejemplos
IF (@RUN_EXAMPLES = 1)
BEGIN
    PRINT N'>> Ejecutando ejemplos...';

    SELECT IDContrato, LiquidoPagable, FechaPago
    FROM dbo.GestionSalarios
    WHERE UPPER(EstadoPago) = 'PAGADO'
      AND Gestion = 2025;

    SELECT IDContrato, IDEmpleado, NumeroContrato, FechaInicio, FechaFin
    FROM dbo.Contratos
    WHERE IDDepartamento = 3 AND IDCargo = 5;

    IF OBJECT_ID(N'dbo.Postulaciones','U') IS NOT NULL
    BEGIN
        SELECT TOP 5 IDPostulacion, IDPostulante, IDVacante, FechaPostulacion
        FROM dbo.Postulaciones
        WHERE Estado = N'Pendiente'
        ORDER BY FechaPostulacion DESC;
    END

    SELECT Nombres, ApellidoPaterno
    FROM dbo.Empleados
    WHERE Estado = 1;

    -- Otros ejemplos… (puedes activar cuando ya tengas datos)
END
ELSE
BEGIN
    PRINT N'>> Ejemplos deshabilitados (@RUN_EXAMPLES = 0).';
END
GO

PRINT N'✅ Instalación completada en [' + DB_NAME() + N'].';
