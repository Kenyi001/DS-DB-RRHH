# Ticket: Implementar API REST para Módulo Contratos

- **ID del Ticket:** `fase2-0004`
- **Fase:** `Sprint 2: Módulo Contratos`
- **Estado:** `Abierto`
- **Prioridad:** `Alta`

---

## Descripción

Desarrollar API REST para gestión de contratos con validación automática de solapes, wizard de creación paso a paso y generación de documentos PDF. Incluye estados de contrato y workflows de aprobación.

---

## Criterios de Aceptación

- [ ] CRUD completo de contratos con validación de solapes
- [ ] Función `fn_ValidarSolapeContrato` implementada y funcionando
- [ ] Estados de contrato: Borrador → Activo → Renovado → Finalizado
- [ ] Wizard de creación valida datos en cada paso
- [ ] Generación automática de PDF de contrato
- [ ] No se permiten contratos superpuestos para mismo empleado
- [ ] API responde en < 2s para validaciones de solape
- [ ] Tests automatizados cubren reglas de negocio críticas

---

## Detalles Técnicos y Notas de Implementación

### Endpoints de Contratos
```http
GET    /api/v1/contratos                    # Lista con filtros
POST   /api/v1/contratos                    # Crear (valida solapes)
GET    /api/v1/contratos/{id}               # Detalle contrato
PUT    /api/v1/contratos/{id}               # Actualizar
DELETE /api/v1/contratos/{id}               # Finalizar contrato
POST   /api/v1/contratos/{id}/renovar       # Renovar contrato
GET    /api/v1/contratos/validar-solape     # Validar fechas antes de crear
```

### Función SQL para Validación de Solapes
```sql
CREATE FUNCTION fn_ValidarSolapeContrato(
    @IDEmpleado INT,
    @FechaInicio DATE,
    @FechaFin DATE,
    @IDContratoExcluir INT = NULL
) RETURNS BIT
AS
BEGIN
    DECLARE @TieneSolape BIT = 0;
    
    IF EXISTS (
        SELECT 1 FROM Contratos
        WHERE IDEmpleado = @IDEmpleado AND Estado = 1
        AND (@IDContratoExcluir IS NULL OR IDContrato <> @IDContratoExcluir)
        AND (
            (@FechaInicio BETWEEN FechaInicio AND ISNULL(FechaFin, '2099-12-31'))
            OR (@FechaFin BETWEEN FechaInicio AND ISNULL(FechaFin, '2099-12-31'))
            OR (FechaInicio BETWEEN @FechaInicio AND @FechaFin)
        )
    )
        SET @TieneSolape = 1;
    
    RETURN @TieneSolape;
END
```

### Estados y Transiciones
- **Borrador**: Editable, no afecta planilla
- **Activo**: En vigor, incluido en cálculos
- **Renovado**: Contrato renovado (estado histórico)
- **Finalizado**: Terminado, no editable

### Generación de PDF
- Laravel PDF package o DomPDF
- Template con datos de contrato y empleado
- Storage en S3/MinIO con referencia en tabla Documentos

---

## Especificaciones Relacionadas

- `/Docs/specs/api-contracts.md` - Endpoints y validaciones
- `/Docs/specs/db-model.md` - Funciones SQL y modelo datos
- `/Docs/specs/ux-design.md` - Wizard UI patterns

---

## Dependencias

- **Bloquea:** `fase3-0005` (Subsidios/Anticipos), `fase4-0006` (Planilla MVP)
- **Bloqueado por:** `fase1-0003` (API Empleados), `fase0-0002` (Datos semilla)

---

## Sub-Tareas

- [ ] Crear modelo Eloquent Contrato con estados y relationships
- [ ] Implementar función fn_ValidarSolapeContrato en migración SQL
- [ ] Desarrollar ContratoService con validaciones de negocio
- [ ] Crear ContratoController con endpoints REST
- [ ] Implementar validaciones en ContratoRequest
- [ ] Añadir estados de contrato con máquina de estados
- [ ] Integrar generación de PDF para contratos
- [ ] Crear tests unitarios para validaciones de solape
- [ ] Implementar tests de integración para estados
- [ ] Añadir documentación Swagger para endpoints

---

## Comentarios y Discusión

**Owner:** [Placeholder - Backend Developer]
**Estimación:** 20-24 horas
**Sprint:** Sprint 2 (Semanas 5-6)

**Nota crítica**: La validación de solapes debe ser atómica para evitar race conditions en creación simultánea de contratos.