


--------------------------INDICES----------------------------
--------------------------INDICES----------------------------
-------------------------------------------------------------



-- Optimiza consultas que buscan salarios pendientes de pago en cierto periodo
CREATE NONCLUSTERED INDEX IX_GestionSalarios_PagosPendientes
ON GestionSalarios (Gestion, Mes)
INCLUDE (IDContrato, LiquidoPagable, FechaPago)

select * from GestionSalarios

SELECT 
    IDContrato, LiquidoPagable, FechaPago
FROM GestionSalarios
WHERE EstadoPago = 'pagado'
  AND Gestion = 2025
  

  select * from GestionSalarios

-- Optimiza búsquedas de empleados activos en un cargo y departamento específico
CREATE NONCLUSTERED INDEX IX_Contratos_ActivoCargoDepto
ON Contratos (IDDepartamento, IDCargo, Estado)
INCLUDE (IDEmpleado, NumeroContrato, FechaInicio, FechaFin)
WHERE Estado = 1;

SELECT 
    IDContrato,
    IDEmpleado,
    NumeroContrato,
    FechaInicio,
    FechaFin
FROM Contratos
WHERE IDDepartamento = 3
  AND IDCargo = 5
  

  select * from Contratos




-- Optimiza consultas de vacaciones pendientes/aprobadas por jefe
CREATE NONCLUSTERED INDEX IX_SolicitudesVacaciones_JefePendientes
ON SolicitudesVacaciones (JefeAprobador, EstadoSolicitud, FechaInicio)
INCLUDE (IDContrato, FechaFin, DiasVacaciones, Observaciones)



SELECT IDContrato, FechaInicio, FechaFin, DiasVacaciones, Observaciones
FROM SolicitudesVacaciones
WHERE JefeAprobador = 'Carlos López'
  AND EstadoSolicitud IN ('Pendiente','Aprobada')
  AND FechaInicio >= '2022-01-01';

  select * from SolicitudesVacaciones


SELECT IDContrato, FechaInicio, FechaFin, DiasVacaciones, Observaciones
FROM SolicitudesVacaciones
WHERE EstadoSolicitud IN ('Pendiente','Aprobada')
 AND FechaInicio BETWEEN '2022-01-14' AND '2022-08-20';

  select * from SolicitudesVacaciones



----------------------empleados activos o inactivos-----------------------

CREATE NONCLUSTERED INDEX IX_Empleados_Departamento_Estado
ON Empleados (Estado)
INCLUDE (Nombres, ApellidoPaterno)


SELECT Nombres, ApellidoPaterno
FROM Empleados
WHERE  Estado = 1;

select * from Empleados

------------------------POSTULACIONES RECIENTES-------------------
CREATE NONCLUSTERED INDEX IX_Postulaciones_Recientes
ON Postulaciones (FechaPostulacion DESC)
WHERE Estado = 'Pendiente';

SELECT TOP 5 IDPostulacion, IDPostulante, IDVacante, FechaPostulacion
FROM Postulaciones
WHERE Estado = 'Pendiente'
ORDER BY FechaPostulacion DESC;







-------------------------------------PROCEDURES--------------------------------
-------------------------------------PROCEDURES--------------------------------
-------------------------------------------------------------------------------


---------Calculo de planilla Mensual---

CREATE OR ALTER PROCEDURE sp_CalcularSalarioMensual
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

    -- 1. Obtener salario básico
    SELECT @Basico = HaberBasico
    FROM Contratos
    WHERE IDContrato = @IDContrato
      AND Estado = 1;  -- solo contratos activos

    -- 2. Sumar subsidios activos
    SELECT @TotalSubsidios = ISNULL(SUM(MontoSubsidio), 0)
    FROM Subsidios
    WHERE IDContrato = @IDContrato
      AND Estado = 1
      AND FechaInicio <= EOMONTH(DATEFROMPARTS(@Gestion, @Mes, 1)) -- activo en ese mes
      AND (FechaFin IS NULL OR FechaFin >= DATEFROMPARTS(@Gestion, @Mes, 1));

    -- 3. Sumar anticipos pendientes en el mes/gestión
    SELECT @TotalAnticipos = ISNULL(SUM(MontoAnticipo), 0)
    FROM Anticipos
    WHERE IDContrato = @IDContrato
      AND EstadoAnticipo = 'Pendiente'
      AND MesDescuento = @Mes
      AND GestionDescuento = @Gestion;

    -- 4. Calcular líquido
    SET @Liquido = @Basico + @TotalSubsidios - @TotalAnticipos;

    -- 5. Insertar o actualizar en Gestión de Salarios
    IF EXISTS (SELECT 1 FROM GestionSalarios 
               WHERE IDContrato = @IDContrato 
                 AND Mes = @Mes 
                 AND Gestion = @Gestion)
        UPDATE GestionSalarios
        SET SalarioBasico = @Basico,
            TotalIngresos = @Basico + @TotalSubsidios,
            TotalDescuentos = @TotalAnticipos,
            LiquidoPagable = @Liquido,
            FechaPago = NULL,
            EstadoPago = 'Pendiente'
        WHERE IDContrato = @IDContrato 
          AND Mes = @Mes 
          AND Gestion = @Gestion;
    ELSE
        INSERT INTO GestionSalarios 
            (IDContrato, Mes, Gestion, SalarioBasico, TotalIngresos, TotalDescuentos, LiquidoPagable)
        VALUES 
            (@IDContrato, @Mes, @Gestion, @Basico, @Basico + @TotalSubsidios, @TotalAnticipos, @Liquido);
END;


EXEC sp_CalcularSalarioMensual @IDContrato = 3, @Mes = 7, @Gestion = 2022;

SELECT *
FROM GestionSalarios
WHERE IDContrato = 3 AND Mes = 7 AND Gestion = 2022;


-- Julio 2022, para empleados con IDContrato 1, 2 y 3
EXEC sp_CalcularSalarioMensual @IDContrato = 1, @Mes = 7, @Gestion = 2022;
EXEC sp_CalcularSalarioMensual @IDContrato = 2, @Mes = 7, @Gestion = 2022;
EXEC sp_CalcularSalarioMensual @IDContrato = 3, @Mes = 7, @Gestion = 2022;

-- Ver los resultados consolidados
SELECT IDContrato, Mes, Gestion, SalarioBasico, TotalIngresos, TotalDescuentos, LiquidoPagable, EstadoPago
FROM GestionSalarios
WHERE Mes = 7 AND Gestion = 2022;


-- Ejecutamos para un rango de contratos (ejemplo ID 1 a 5) en agosto 2022
EXEC sp_CalcularSalarioMensual @IDContrato = 1, @Mes = 8, @Gestion = 2022;
EXEC sp_CalcularSalarioMensual @IDContrato = 2, @Mes = 8, @Gestion = 2022;
EXEC sp_CalcularSalarioMensual @IDContrato = 3, @Mes = 8, @Gestion = 2022;
EXEC sp_CalcularSalarioMensual @IDContrato = 4, @Mes = 8, @Gestion = 2022;
EXEC sp_CalcularSalarioMensual @IDContrato = 5, @Mes = 8, @Gestion = 2022;

-- Resumen de la planilla de agosto 2022
SELECT 
    Mes, Gestion,
    SUM(SalarioBasico) AS TotalBasico,
    SUM(TotalIngresos) AS TotalIngresos,
    SUM(TotalDescuentos) AS TotalDescuentos,
    SUM(LiquidoPagable) AS TotalLiquido
FROM GestionSalarios
WHERE Mes = 8 AND Gestion = 2022
GROUP BY Mes, Gestion;



-- Ejecutamos de enero a marzo 2023 para contrato 3
EXEC sp_CalcularSalarioMensual @IDContrato = 3, @Mes = 1, @Gestion = 2023;
EXEC sp_CalcularSalarioMensual @IDContrato = 3, @Mes = 2, @Gestion = 2023;
EXEC sp_CalcularSalarioMensual @IDContrato = 3, @Mes = 3, @Gestion = 2023;

-- Consultamos la evolución del salario en esos meses
SELECT Mes, Gestion, SalarioBasico, TotalIngresos, TotalDescuentos, LiquidoPagable, EstadoPago
FROM GestionSalarios
WHERE IDContrato = 3 AND Gestion = 2023
ORDER BY Mes;




--------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------













----------genera un resumen anual con categorías de desempeño----------------------

CREATE PROCEDURE sp_ReporteEvaluacionAnual
    @Gestion INT
AS
BEGIN
    SET NOCOUNT ON;

    ;WITH Promedios AS (
        SELECT 
            E.IDEmpleado,
            AVG(Ev.Calificacion * 1.0) AS PromedioCalificacion,
            COUNT(Ev.IDEvaluacion) AS CantidadEvaluaciones,
            MAX(Ev.FechaEvaluacion) AS UltimaEvaluacion
        FROM Empleados E
        INNER JOIN Evaluaciones Ev ON E.IDEmpleado = Ev.IDEmpleado
        WHERE Ev.Gestion = @Gestion
        GROUP BY E.IDEmpleado
    )
    SELECT 
        E.IDEmpleado,
        E.Nombres + ' ' + E.ApellidoPaterno + ISNULL(' ' + E.ApellidoMaterno, '') AS NombreCompleto,
        D.NombreDepartamento,
        Cg.NombreCargo,
        Ct.NombreCategoria,
        P.PromedioCalificacion,
        P.CantidadEvaluaciones,
        P.UltimaEvaluacion,
        CASE 
            WHEN P.PromedioCalificacion >= 4.5 THEN 'Excelente'
            WHEN P.PromedioCalificacion >= 3.5 THEN 'Bueno'
            WHEN P.PromedioCalificacion >= 2.5 THEN 'Regular'
            ELSE 'Deficiente'
        END AS CategoriaDesempeno,
        -- Antigüedad en años (usando la fecha del contrato)
        DATEDIFF(YEAR, Ct2.FechaInicio, GETDATE()) AS AntiguedadAnios,
        -- Información contractual
        Ct2.TipoContrato,
        Ct2.HaberBasico,
        -- Subsidios totales del año
        ISNULL((
            SELECT SUM(Sb.MontoSubsidio) 
            FROM Subsidios Sb 
            WHERE Sb.IDContrato = Ct2.IDContrato 
              AND YEAR(Sb.FechaInicio) = @Gestion
        ),0) AS TotalSubsidios,
        -- Vacaciones tomadas en el año
        ISNULL((
            SELECT SUM(Sv.DiasVacaciones)
            FROM SolicitudesVacaciones Sv
            WHERE Sv.IDContrato = Ct2.IDContrato 
              AND YEAR(Sv.FechaInicio) = @Gestion
              AND Sv.EstadoSolicitud = 'Tomada'
        ),0) AS DiasVacacionesTomadas,
        -- Anticipos solicitados
        ISNULL((
            SELECT SUM(A.MontoAnticipo)
            FROM Anticipos A
            WHERE A.IDContrato = Ct2.IDContrato 
              AND A.GestionDescuento = @Gestion
        ),0) AS TotalAnticipos,
        -- Salarios liquidados en el año
        ISNULL((
            SELECT SUM(Gs.LiquidoPagable)
            FROM GestionSalarios Gs
            WHERE Gs.IDContrato = Ct2.IDContrato 
              AND Gs.Gestion = @Gestion
        ),0) AS TotalLiquidoPagado,
        -- Evaluador más frecuente
        (SELECT TOP 1 Ev2.Evaluador
         FROM Evaluaciones Ev2
         WHERE Ev2.IDEmpleado = E.IDEmpleado 
           AND Ev2.Gestion = @Gestion
         GROUP BY Ev2.Evaluador
         ORDER BY COUNT(*) DESC) AS EvaluadorFrecuente
    FROM Promedios P
    INNER JOIN Empleados E ON E.IDEmpleado = P.IDEmpleado
    INNER JOIN Contratos Ct2 ON Ct2.IDEmpleado = E.IDEmpleado AND Ct2.Estado = 1
    INNER JOIN Categorias Ct ON Ct.IDCategoria = Ct2.IDCategoria
    INNER JOIN Cargos Cg ON Cg.IDCargo = Ct2.IDCargo
    INNER JOIN Departamentos D ON D.IDDepartamento = Ct2.IDDepartamento
    ORDER BY P.PromedioCalificacion DESC;
END;
GO


-- Gestión 2025
EXEC sp_ReporteEvaluacionAnual @Gestion = 2025;

-- Gestión 2024
EXEC sp_ReporteEvaluacionAnual @Gestion = 2024;

-- Gestión 2023
EXEC sp_ReporteEvaluacionAnual 2023;  -- también puedes pasarlo sin nombrar el parámetro
select * from Empleados
select * from GestionSalarios

-- Gestión actual (dinámico)
DECLARE @AnioActual INT = YEAR(GETDATE());
EXEC sp_ReporteEvaluacionAnual @Gestion = @AnioActual;



SELECT 
    e.IDEmpleado,
    MAX(e.Calificacion) AS MejorCalificacion,
    MIN(e.Calificacion) AS PeorCalificacion,
    (MAX(e.Calificacion) - MIN(e.Calificacion)) AS Diferencia
FROM Evaluaciones e
WHERE e.Gestion = 2024
GROUP BY e.IDEmpleado
HAVING MAX(e.Calificacion) <> MIN(e.Calificacion);




;WITH UltimaEval AS (
    SELECT 
        IDEmpleado, 
        Gestion, 
        DATEPART(QUARTER, FechaEvaluacion) AS Trimestre, 
        Calificacion, 
        FechaEvaluacion, 
        Evaluador,
        ROW_NUMBER() OVER (PARTITION BY IDEmpleado, Gestion ORDER BY FechaEvaluacion DESC) AS rn
    FROM Evaluaciones)

SELECT 
    IDEmpleado,
    Gestion,
    AVG(Calificacion) AS PromedioCalificacion,
    COUNT(*) AS CantidadEvaluaciones
FROM Evaluaciones
GROUP BY IDEmpleado, Gestion
HAVING AVG(Calificacion) > 4.5
ORDER BY PromedioCalificacion DESC;








----------------------------------TRIGGER-----------------------------------------------------
----------------------------------TRIGGER----------------------------------------------------
-------------------------------------------------------------------------------------------------



-----------------No permitir insertar un anticipo que supere el 50% del salario básico vigente del contrato.------------
----------------evita abusos de anticipos y asegura cumplimiento de políticas de la empresa-------------------------

CREATE TRIGGER trg_Validar_Anticipo
ON Anticipos
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Validaciones múltiples
    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN Contratos c ON i.IDContrato = c.IDContrato
        WHERE i.MontoAnticipo > (c.HaberBasico * 0.5)
    )
    BEGIN
        RAISERROR('? El anticipo no puede superar el 50%% del salario básico vigente del contrato.',16,1);
        ROLLBACK TRANSACTION;
        RETURN;
    END;

    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN Contratos c ON i.IDContrato = c.IDContrato
        WHERE c.Estado = 0 OR (c.FechaFin IS NOT NULL AND c.FechaFin < GETDATE())
    )
    BEGIN
        RAISERROR('? El contrato está inactivo o finalizado. No se pueden registrar anticipos.',16,1);
        ROLLBACK TRANSACTION;
        RETURN;
    END;

    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN Anticipos a ON i.IDContrato = a.IDContrato
        WHERE a.EstadoAnticipo = 'Pendiente'
    )
    BEGIN
        RAISERROR('? Ya existe un anticipo pendiente para este contrato. Debe cancelarse o descontarse antes de solicitar otro.',16,1);
        ROLLBACK TRANSACTION;
        RETURN;
    END;

    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN Finiquitos f ON i.IDContrato = f.IDContrato
        WHERE f.EstadoFiniquito = 'Pagado' OR f.EstadoFiniquito = 'Calculado'
    )
    BEGIN
        RAISERROR('? El empleado ya tiene un finiquito asociado, no puede solicitar anticipos.',16,1);
        ROLLBACK TRANSACTION;
        RETURN;
    END;

    -- Si todo es válido, insertar
    INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
    SELECT 
        IDContrato, 
        MontoAnticipo, 
        ISNULL(FechaAnticipo, GETDATE()), -- Si no manda fecha, se asigna la actual
        MesDescuento, 
        GestionDescuento, 
        EstadoAnticipo
    FROM inserted;

END;
GO



 select * from Evaluaciones
 select * from Anticipos
 select * from Contratos
 select * from Finiquitos
 select * from Empleados

 INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (2, 3000, GETDATE(), 9, 2025, 'Pendiente'); -- Haber básico = 5200, supera el 50%

 INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (2, 300, GETDATE(), 9, 2025, 'Pendiente'); -- Haber básico no supera el 50%

INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (9, 3000, GETDATE(), 9, 2025, 'Pendiente'); -- Haber básico = 5000, supera el 50%

INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (9, 100, GETDATE(), 9, 2025, 'Pendiente'); -- Haber básico no supera el 50%



INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (12, 4000, GETDATE(), 9, 2025, 'pendiente');   -- ya descontado, permite pedir otro

INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (12, 500, GETDATE(), 9, 2025, 'pendiente');   -- ya descontado, permite pedir otro

INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (13, 4000, GETDATE(), 9, 2025, 'pendiente');   -- ya descontado, permite pedir otro

INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaAnticipo, MesDescuento, GestionDescuento, EstadoAnticipo)
VALUES (18, 300, GETDATE(), 9, 2025, 'pendiente');   -- ya descontado, permite pedir otro

select * from GestionSalarios
select * from Contratos
select * from Anticipos

update Anticipos
set EstadoAnticipo = 'Descontado'
where IDAnticipo=12;

----------------------------------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------



----------------------- subsidios y Gestion de Salarios.----------------------------
select * from TiposSubsidio

CREATE TRIGGER TRG_Subsidios_A_GestionSalarios
ON Subsidios
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Procesar todos los subsidios insertados
    ;WITH SubsidiosProcesados AS (
        SELECT 
            i.IDSubsidio,
            i.IDContrato,
            i.MontoSubsidio,
            MONTH(i.FechaInicio) AS MesSubsidio,
            YEAR(i.FechaInicio) AS GestionSubsidio
        FROM inserted i
        INNER JOIN Contratos c ON i.IDContrato = c.IDContrato
        WHERE i.Estado = 1 
          AND c.Estado = 1
          AND (c.FechaFin IS NULL OR c.FechaFin >= i.FechaInicio)
    )
    SELECT * INTO #SubsidiosTemp FROM SubsidiosProcesados; -- Convertimos CTE en tabla temporal

    -- Validación: prevenir subsidios duplicados para mismo contrato/mes/gestión
    IF EXISTS (
        SELECT 1
        FROM #SubsidiosTemp sp
        INNER JOIN Subsidios s
            ON sp.IDContrato = s.IDContrato
           AND MONTH(s.FechaInicio) = sp.MesSubsidio
           AND YEAR(s.FechaInicio) = sp.GestionSubsidio
           AND s.IDSubsidio <> sp.IDSubsidio
           AND s.Estado = 1
    )
    BEGIN
        RAISERROR('? Ya existe un subsidio activo para este contrato en el mismo mes y gestión.',16,1);
        ROLLBACK TRANSACTION;
        RETURN;
    END;

    -- Insertar o actualizar en Gestión de Salarios
    MERGE GestionSalarios AS gs
    USING #SubsidiosTemp sp
    ON gs.IDContrato = sp.IDContrato
       AND gs.Mes = sp.MesSubsidio
       AND gs.Gestion = sp.GestionSubsidio
    WHEN MATCHED THEN
        UPDATE SET 
            gs.TotalIngresos = gs.TotalIngresos + sp.MontoSubsidio,
            gs.LiquidoPagable = gs.LiquidoPagable + sp.MontoSubsidio
    WHEN NOT MATCHED THEN
        INSERT (IDContrato, Mes, Gestion, DiasTrabajos, SalarioBasico, TotalIngresos, TotalDescuentos, LiquidoPagable, FechaPago, EstadoPago)
        VALUES (
            sp.IDContrato,
            sp.MesSubsidio,
            sp.GestionSubsidio,
            30,
            (SELECT TOP 1 HaberBasico FROM Contratos WHERE IDContrato = sp.IDContrato),
            (SELECT TOP 1 HaberBasico FROM Contratos WHERE IDContrato = sp.IDContrato) + sp.MontoSubsidio,
            0,
            (SELECT TOP 1 HaberBasico FROM Contratos WHERE IDContrato = sp.IDContrato) + sp.MontoSubsidio,
            NULL,
            'Pendiente'
        );

    DROP TABLE #SubsidiosTemp; -- limpiar tabla temporal
END;
GO



select * from Categorias
select * from Cargos
select * from Departamentos
select * from Contratos
select * from Empleados
select * from Subsidios
select * from TiposSubsidio
select * from GestionSalarios


INSERT INTO Subsidios Values(17,9,400,'2025-03-01','2025-03-30','Sin Observacion',1)

select *from GestionSalarios 



update Subsidios
set Estado=0
where IDSubsidio = 12;


---------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------






--------------------------------------------------------FUNCIONES------------------------------------------------------------------
--------------------------------------------------------FUNCIONES------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------







----------------------------CALCULO SALARIAL-----------------------------------------


CREATE FUNCTION dbo.fn_CalcularSalarioTotal
(
    @IDEmpleado INT,
    @FechaInicio DATE,
    @FechaFin DATE
)
RETURNS DECIMAL(12,2)
AS
BEGIN
    DECLARE @Total DECIMAL(12,2) = 0;

    -- Obtener el contrato activo del empleado
    DECLARE @IDContrato INT;
    DECLARE @IDCategoria INT;

    SELECT TOP 1 
        @IDContrato = c.IDContrato,
        @IDCategoria = c.IDCategoria
    FROM Contratos c
    WHERE c.IDEmpleado = @IDEmpleado
      AND c.Estado = 1
      AND (c.FechaFin IS NULL OR c.FechaFin >= @FechaInicio)
    ORDER BY c.FechaInicio DESC;

    IF @IDContrato IS NULL
        RETURN 0; -- Sin contrato activo

    -- Total salarios dentro del rango
    SELECT @Total = ISNULL(SUM(gs.LiquidoPagable),0)
    FROM GestionSalarios gs
    WHERE gs.IDContrato = @IDContrato
      AND gs.FechaPago BETWEEN @FechaInicio AND @FechaFin
      AND gs.EstadoPago = 'Pagado';

    -- Sumar ajustes salariales vigentes
    SELECT @Total = @Total + ISNULL(SUM(a.HaberBasico),0)
    FROM AjustesSalariales a
    WHERE a.IDCategoria = @IDCategoria
      AND a.FechaVigencia <= @FechaFin
      AND (a.FechaFinVigencia IS NULL OR a.FechaFinVigencia >= @FechaInicio)
      AND a.Estado = 1;

    -- Sumar subsidios activos en el rango
    SELECT @Total = @Total + ISNULL(SUM(s.MontoSubsidio),0)
    FROM Subsidios s
    WHERE s.IDContrato = @IDContrato
      AND s.FechaInicio <= @FechaFin
      AND (s.FechaFin IS NULL OR s.FechaFin >= @FechaInicio)
      AND s.Estado = 1;

    -- Restar anticipos descontados en el rango
    SELECT @Total = @Total - ISNULL(SUM(a.MontoAnticipo),0)
    FROM Anticipos a
    WHERE a.IDContrato = @IDContrato
      AND a.FechaAnticipo BETWEEN @FechaInicio AND @FechaFin
      AND a.EstadoAnticipo = 'Descontado';

    RETURN @Total;
END;

DECLARE @PresupuestoMensual DECIMAL(12,2) = 100000;

SELECT 
    SUM(dbo.fn_CalcularSalarioTotal(e.IDEmpleado, '2025-08-01', '2025-08-31')) AS GastoNomina,
    @PresupuestoMensual - SUM(dbo.fn_CalcularSalarioTotal(e.IDEmpleado, '2025-08-01', '2025-08-31')) AS Diferencia
FROM Empleados e;



---------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------


---------------Calcula todos los beneficios de un empleado en un período específico, sumando salarios, subsidios, vacaciones y lactancia-------------

CREATE FUNCTION dbo.fn_TotalBeneficiosPeriodo
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
        e.Nombres + ' ' + e.ApellidoPaterno + ' ' + e.ApellidoMaterno AS Empleado,
        SUM(gs.LiquidoPagable) AS TotalSalarios,
        SUM(sub.MontoSubsidio) AS TotalSubsidios,
        SUM(CASE WHEN sv.EstadoSolicitud='Tomada' THEN gs.SalarioBasico * sv.DiasVacaciones/30.0 ELSE 0 END) AS BonificacionVacaciones,
        SUM(el.MontoEntregado) AS TotalLactancia,
        SUM(f.LiquidoFiniquito) AS TotalFiniquito
    FROM Empleados e
    INNER JOIN Contratos c ON e.IDEmpleado = c.IDEmpleado
    LEFT JOIN GestionSalarios gs ON c.IDContrato = gs.IDContrato AND gs.FechaPago BETWEEN @FechaInicio AND @FechaFin
    LEFT JOIN Subsidios sub ON c.IDContrato = sub.IDContrato AND sub.FechaInicio <= @FechaFin
    LEFT JOIN SolicitudesVacaciones sv ON c.IDContrato = sv.IDContrato AND sv.FechaInicio BETWEEN @FechaInicio AND @FechaFin
    LEFT JOIN EntregasLactancia el ON c.IDContrato = (SELECT IDContrato FROM SolicitudesLactancia sl WHERE sl.IDSolicitudLactancia = el.IDSolicitudLactancia)
    LEFT JOIN Finiquitos f ON c.IDContrato = f.IDContrato AND f.FechaRetiro BETWEEN @FechaInicio AND @FechaFin
    GROUP BY c.IDContrato, e.Nombres, e.ApellidoPaterno, e.ApellidoMaterno
);



SELECT e.IDEmpleado, e.Nombres, f.TotalSalarios, f.TotalSubsidios, f.BonificacionVacaciones
FROM Empleados e
CROSS APPLY dbo.fn_TotalBeneficiosPeriodo(e.IDEmpleado, '2025-01-01', '2025-12-31') f;










----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------






















