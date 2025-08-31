# Ticket: Implementar Triggers SQL Críticos

- **ID del Ticket:** `fase4-0006`
- **Fase:** `Sprint 4: Planilla MVP`
- **Estado:** `Abierto`
- **Prioridad:** `Alta`

---

## Descripción

Implementar triggers críticos de validación e integridad para garantizar reglas de negocio a nivel de base de datos, incluyendo validación de anticipos, propagación de subsidios y auditoría automática.

---

## Criterios de Aceptación

- [ ] `trg_Validar_Anticipo` impide anticipos > 50% haber básico
- [ ] `TRG_Subsidios_A_GestionSalarios` propaga cambios automáticamente
- [ ] `trg_Empleados_Audit` registra todos los cambios en AuditLog
- [ ] Triggers manejan errores correctamente con RAISERROR
- [ ] Performance de triggers < 100ms para operaciones normales
- [ ] Tests automatizados validan comportamiento de triggers
- [ ] Rollback automático en caso de validaciones fallidas
- [ ] Documentación completa de cada trigger

---

## Detalles Técnicos y Notas de Implementación

### trg_Validar_Anticipo (INSTEAD OF INSERT)
```sql
CREATE TRIGGER trg_Validar_Anticipo
ON Anticipos
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Validar tope 50% haber básico
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
    
    -- Validar no existe anticipo pendiente
    IF EXISTS (
        SELECT 1 FROM inserted i
        WHERE EXISTS (
            SELECT 1 FROM Anticipos a 
            WHERE a.IDContrato = i.IDContrato 
            AND a.EstadoAnticipo IN ('Pendiente', 'Aprobado')
        )
    )
    BEGIN
        RAISERROR('Ya existe un anticipo pendiente para este contrato.', 16, 1);
        RETURN;
    END;
    
    -- Insertar si todas las validaciones pasan
    INSERT INTO Anticipos (IDContrato, MontoAnticipo, FechaSolicitud, EstadoAnticipo, Usuario, FechaCreacion)
    SELECT IDContrato, MontoAnticipo, FechaSolicitud, EstadoAnticipo, Usuario, GETDATE()
    FROM inserted;
END
```

### TRG_Subsidios_A_GestionSalarios (AFTER INSERT/UPDATE)
```sql
CREATE TRIGGER TRG_Subsidios_A_GestionSalarios
ON Subsidios
AFTER INSERT, UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Actualizar GestionSalarios para contratos afectados
    UPDATE gs
    SET TotalSubsidios = subsidios_calc.Total,
        LiquidoPagable = gs.HaberBasico + subsidios_calc.Total - gs.TotalDescuentos,
        FechaModificacion = GETDATE()
    FROM GestionSalarios gs
    INNER JOIN (
        SELECT s.IDContrato, 
               SUM(CASE WHEN s.Estado = 1 THEN s.Monto ELSE 0 END) AS Total
        FROM Subsidios s
        INNER JOIN inserted i ON s.IDContrato = i.IDContrato
        GROUP BY s.IDContrato
    ) subsidios_calc ON gs.IDContrato = subsidios_calc.IDContrato;
END
```

### Performance y Testing
- Triggers deben ejecutar en < 100ms para operaciones estándar
- Tests de integración con SQL Server container en CI
- Validar rollback automático en casos de error

---

## Especificaciones Relacionadas

- `/Docs/specs/sql-objects.md` - Objetos SQL y triggers
- `/Docs/specs/db-model.md` - Modelo de datos y constraints
- `/Docs/specs/security.md` - Auditoría y trazabilidad

---

## Dependencias

- **Bloquea:** `fase4-0005` (Planilla MVP testing), `fase3-0007` (UI Subsidios)
- **Bloqueado por:** `fase2-0004` (API Contratos), `fase0-0002` (Datos semilla)

---

## Sub-Tareas

- [ ] Crear migración para trigger trg_Validar_Anticipo
- [ ] Implementar trigger TRG_Subsidios_A_GestionSalarios  
- [ ] Desarrollar trigger trg_Empleados_Audit para auditoría
- [ ] Añadir índices optimizados para performance de triggers
- [ ] Crear tests de integración para validación de anticipos
- [ ] Implementar tests para propagación de subsidios
- [ ] Validar manejo de errores y RAISERROR
- [ ] Documentar comportamiento esperado de cada trigger
- [ ] Optimizar queries dentro de triggers
- [ ] Añadir métricas de performance para triggers

---

## Comentarios y Discusión

**Owner:** [Placeholder - DBA + Backend Developer]
**Estimación:** 16-20 horas
**Sprint:** Sprint 4 (Semanas 9-10)

**Nota importante**: Triggers requieren revisión obligatoria por DBA antes de deployment a producción debido a su impacto en performance y integridad de datos.