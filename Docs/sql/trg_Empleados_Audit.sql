-- Trigger trg_Empleados_Audit
CREATE OR ALTER TRIGGER trg_Empleados_Audit
ON Empleados
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;
    INSERT INTO AuditLog (entity, entity_id, action, user_id, created_at, payload_before, payload_after, trace_id)
    SELECT 'Empleados', COALESCE(i.IDEmpleado, d.IDEmpleado),
           CASE WHEN i.IDEmpleado IS NOT NULL AND d.IDEmpleado IS NULL THEN 'INSERT'
                WHEN i.IDEmpleado IS NOT NULL AND d.IDEmpleado IS NOT NULL THEN 'UPDATE'
                ELSE 'DELETE' END,
           SUSER_SNAME(), SYSUTCDATETIME(),
           (SELECT d.* FOR JSON PATH, WITHOUT_ARRAY_WRAPPER),
           (SELECT i.* FOR JSON PATH, WITHOUT_ARRAY_WRAPPER),
           NULL
    FROM inserted i FULL OUTER JOIN deleted d ON i.IDEmpleado = d.IDEmpleado;
END
GO
