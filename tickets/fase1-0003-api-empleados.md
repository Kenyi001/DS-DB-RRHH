# Ticket: Implementar API REST para Módulo Empleados

- **ID del Ticket:** `fase1-0003`
- **Fase:** `Sprint 1: Módulo Empleados`
- **Estado:** `Abierto`
- **Prioridad:** `Alta`

---

## Descripción

Desarrollar la API REST completa para el módulo de Empleados incluyendo CRUD operations, validaciones robustas, auditoría automática y endpoints optimizados para la interfaz de usuario.

---

## Criterios de Aceptación

- [ ] Endpoints CRUD empleados implementados según contratos API
- [ ] Validaciones de CI boliviano, email y datos personales funcionando
- [ ] Paginación server-side para listados grandes (335+ empleados)
- [ ] Filtros de búsqueda por nombre, departamento, estado
- [ ] Trigger de auditoría registra cambios en AuditLog
- [ ] Response time P95 < 2s para operaciones CRUD
- [ ] Tests unitarios con cobertura ≥ 80%
- [ ] Documentación OpenAPI/Swagger generada

---

## Detalles Técnicos y Notas de Implementación

### Endpoints Requeridos
```http
GET    /api/v1/empleados              # Lista paginada con filtros
POST   /api/v1/empleados              # Crear empleado
GET    /api/v1/empleados/{id}         # Detalle empleado
PUT    /api/v1/empleados/{id}         # Actualizar empleado  
DELETE /api/v1/empleados/{id}         # Soft delete empleado
GET    /api/v1/empleados/{id}/contratos # Contratos del empleado
```

### Validaciones Críticas
```php
// EmpleadoRequest validations
'ci' => ['required', 'string', 'regex:/^\d{7,8}$/', 'unique:empleados,ci'],
'nombres' => ['required', 'string', 'max:100', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/'],
'apellido_paterno' => ['required', 'string', 'max:50'],
'fecha_nacimiento' => ['required', 'date', 'before:18 years ago', 'after:70 years ago'],
'email' => ['required', 'email:rfc,dns', 'max:150', 'unique:empleados,email'],
'telefono' => ['nullable', 'regex:/^\+?591[0-9]{8}$/']
```

### Repository Pattern
- EmpleadoRepository con interface para testabilidad
- Métodos optimizados: `findWithFilters()`, `findByDepartamento()`, `getActiveEmployees()`
- Eloquent relationships pre-cargadas para evitar N+1 queries

### Auditoría Implementación
- Trigger `trg_Empleados_Audit` debe capturar INSERT/UPDATE/DELETE
- Payload JSON con datos before/after
- Trace ID correlation para debugging

---

## Especificaciones Relacionadas

- `/Docs/specs/api-contracts.md` - Contratos API y formatos estándar
- `/Docs/specs/db-model.md` - Modelo de datos y relaciones
- `/Docs/specs/security.md` - Validaciones y permisos

---

## Dependencias

- **Bloquea:** `fase1-0004` (UI Empleados), `fase2-0005` (API Contratos)
- **Bloqueado por:** `fase0-0001` (Infraestructura), `fase0-0002` (Datos semilla)

---

## Sub-Tareas

- [ ] Crear Modelo Eloquent Empleado con relationships
- [ ] Implementar EmpleadoRepository con interface
- [ ] Desarrollar EmpleadoService con lógica de negocio
- [ ] Crear EmpleadoController con endpoints REST
- [ ] Implementar FormRequests con validaciones robustas
- [ ] Añadir trigger trg_Empleados_Audit en migración
- [ ] Crear EmpleadoResource para serialización API
- [ ] Implementar tests unitarios para Service y Repository
- [ ] Añadir tests de integración para endpoints API
- [ ] Generar documentación Swagger automática

---

## Comentarios y Discusión

**Owner:** [Placeholder - Backend Developer]
**Estimación:** 16-20 horas
**Sprint:** Sprint 1 (Semanas 3-4)

**Nota técnica**: Implementar soft delete con campos `FechaBaja` y `UsuarioBaja` en lugar de usar Laravel's SoftDeletes trait para mayor control.