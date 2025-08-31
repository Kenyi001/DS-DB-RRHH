-- Trigger trg_Validar_Anticipo (INSTEAD OF INSERT)
CREATE OR ALTER TRIGGER trg_Validar_Anticipo
ON Anticipos
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    IF EXISTS (
        SELECT 1
        FROM inserted i
        INNER JOIN Contratos c ON i.IDContrato = c.IDContrato
        WHERE i.MontoAnticipo > (c.HaberBasico * 0.5)
    )
    BEGIN
        RAISERROR('El anticipo no puede superar el 50% del salario básico vigente.', 16, 1);
        RETURN;
    END;

    -- Validaciones adicionales y forward insert
    INSERT INTO Anticipos (/* columnas */)
    SELECT /* columnas */ FROM inserted;
END
GO
