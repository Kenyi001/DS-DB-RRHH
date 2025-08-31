# Ticket: Implementar Sistema de Planilla MVP

- **ID del Ticket:** `fase4-0005`
- **Fase:** `Sprint 4: Planilla MVP`
- **Estado:** `Abierto`
- **Prioridad:** `Crítica`

---

## Descripción

Implementar el sistema central de generación de planilla mensual incluyendo stored procedures críticos, application locks para exclusividad, tracking de procesos y API REST con soporte para operaciones asíncronas.

---

## Criterios de Aceptación

- [ ] `sp_GenerarPlanillaMensual` genera planilla para 335 empleados en < 30s
- [ ] Idempotencia funciona correctamente con IdempotencyKey
- [ ] Application locks previenen generaciones concurrentes del mismo período
- [ ] Tabla LogPlanilla registra progreso y errores
- [ ] API endpoint `/planilla/generar` responde con 202 Accepted
- [ ] Queue job procesa generación asíncronamente
- [ ] UI muestra progreso en tiempo real
- [ ] Cálculos exactos validados con casos de prueba

---

## Detalles Técnicos y Notas de Implementación

### Stored Procedures Críticos
```sql
-- sp_GenerarPlanillaMensual: Proceso principal
-- Inputs: @Mes, @Gestion, @Usuario, @IdempotencyKey
-- Output: @PlanillaId
-- Efectos: INSERT/UPDATE en GestionSalarios, LogPlanilla

-- sp_CalcularSalarioMensual: Cálculo por contrato
-- Inputs: @IDContrato, @Mes, @Gestion  
-- Efectos: MERGE en GestionSalarios con haber + subsidios - anticipos
```

### Idempotencia y Concurrencia
- Application lock: `sp_getapplock` con resource `planilla_YYYY_MM`
- IdempotencyKey único por request para evitar duplicados
- Tabla LogPlanilla con estados: Iniciado → Completado/Error

### Queue Job Implementation
```php
class GenerarPlanillaJob implements ShouldQueue
{
    public function handle(): void
    {
        DB::transaction(function () {
            DB::statement('EXEC sp_GenerarPlanillaMensual ?, ?, ?, ?, ?', [
                $this->mes, $this->gestion, $this->usuario, $this->idempotencyKey, 0
            ]);
        });
    }
}
```

### API Response Pattern
```json
// POST /api/v1/planilla/generar
// Response 202 Accepted
{
  "success": true,
  "code": 0,
  "data": {
    "planilla_id": 123,
    "status": "Iniciado",
    "estimated_duration": "25s"
  },
  "links": {
    "status_url": "/api/v1/planilla/status/123"
  }
}
```

---

## Especificaciones Relacionadas

- `/Docs/specs/sql-objects.md` - Stored procedures y triggers
- `/Docs/specs/api-contracts.md` - Contratos API REST
- `/Docs/specs/observability.md` - Logging y métricas

---

## Dependencias

- **Bloquea:** `fase5-0007` (Reportes), `fase4-0008` (Triggers SQL)
- **Bloqueado por:** `fase2-0004` (API Contratos), `fase3-0006` (Subsidios/Anticipos)

---

## Sub-Tareas

- [ ] Crear migración con tabla LogPlanilla
- [ ] Implementar sp_GenerarPlanillaMensual con application locks
- [ ] Desarrollar sp_CalcularSalarioMensual con MERGE operations
- [ ] Crear PlanillaService con lógica de orquestación
- [ ] Implementar PlanillaController con endpoints REST
- [ ] Añadir GenerarPlanillaJob para procesamiento asíncrono
- [ ] Crear validaciones para mes/gestión válidos
- [ ] Implementar sistema de tracking de progreso
- [ ] Añadir tests unitarios para cálculos críticos
- [ ] Crear tests de integración con SQL Server container
- [ ] Implementar métricas de performance y alertas

---

## Comentarios y Discusión

**Owner:** [Placeholder - Senior Backend Developer]
**Estimación:** 28-32 horas
**Sprint:** Sprint 4 (Semanas 9-10)

**Nota crítica**: Este es el módulo más complejo del sistema. Requiere coordinación estrecha con DBA para optimización de SPs y validation exhaustiva de cálculos con casos reales.