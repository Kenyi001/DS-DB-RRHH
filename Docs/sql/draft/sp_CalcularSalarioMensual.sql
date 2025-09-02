-- sp_CalcularSalarioMensual (plantilla)
CREATE OR ALTER PROCEDURE sp_CalcularSalarioMensual
    @IDContrato INT,
    @Mes INT,
    @Gestion INT
AS
BEGIN
    SET NOCOUNT ON;

    -- Obtener datos del contrato y empleado
    -- Calcular haberes, subsidios, anticipos y deducciones
    -- Calcular liquido y MERGE en GestionSalarios

    -- Ejemplo (esqueleto):
    DECLARE @HaberBasico DECIMAL(18,2) = 0;
    SELECT @HaberBasico = HaberBasico FROM Contratos WHERE IDContrato = @IDContrato;

    -- Calculos mock
    DECLARE @Liquido DECIMAL(18,2) = ISNULL(@HaberBasico,0);

    MERGE INTO GestionSalarios AS target
    USING (SELECT @IDContrato AS IDContrato, @Mes AS Mes, @Gestion AS Gestion, @Liquido AS LiquidoPagable) AS source
    ON target.IDContrato = source.IDContrato AND target.Mes = source.Mes AND target.Gestion = source.Gestion
    WHEN MATCHED THEN
      UPDATE SET LiquidoPagable = source.LiquidoPagable
    WHEN NOT MATCHED THEN
      INSERT (IDContrato, Mes, Gestion, LiquidoPagable) VALUES (source.IDContrato, source.Mes, source.Gestion, source.LiquidoPagable);
END
GO
