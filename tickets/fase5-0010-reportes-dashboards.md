# Ticket: Implementar Reportes y Dashboards

- **ID del Ticket:** `fase5-0010`
- **Fase:** `Sprint 5: Reportes y Dashboards`
- **Estado:** `Abierto`
- **Prioridad:** `Media`

---

## Descripción

Desarrollar dashboard ejecutivo con KPIs críticos y sistema de reportes con exports en múltiples formatos (PDF, Excel, CSV). Incluye cache inteligente y optimizaciones para queries pesadas.

---

## Criterios de Aceptación

- [ ] Dashboard principal carga < 3s con KPIs agregados
- [ ] Reportes de planilla exportables en PDF/Excel/CSV
- [ ] Cache Redis reduce tiempo de reportes pesados en 50%
- [ ] Filtros avanzados por período, departamento, empleado
- [ ] Gráficos interactivos para tendencias salariales
- [ ] Permisos granulares: solo usuarios autorizados ven datos sensibles
- [ ] Reportes se generan sin timeouts para 335+ empleados
- [ ] UI responsive para dashboards en móvil y desktop

---

## Detalles Técnicos y Notas de Implementación

### KPIs del Dashboard Principal
```php
// DashboardService - métricas críticas
class DashboardService
{
    public function getKPIs(): array
    {
        return [
            'empleados_activos' => $this->getEmpleadosActivos(),
            'contratos_por_vencer' => $this->getContratosPorVencer(30), // 30 días
            'planilla_actual' => $this->getPlanillaEstadoActual(),
            'anticipos_pendientes' => $this->getAnticiposPendientes(),
            'subsidios_activos' => $this->getSubsidiosActivos(),
            'vacaciones_pendientes' => $this->getVacacionesPendientes()
        ];
    }
}
```

### Reportes Críticos
- **Planilla Mensual**: Detalle por empleado con subsidios y descuentos
- **Resumen Departamental**: Totales por departamento y período
- **Histórico Salarial**: Evolución salarial por empleado
- **Subsidios Activos**: Listado con vigencias y montos
- **Anticipos**: Status y programación de descuentos

### Sistema de Cache
```php
// Cache strategy para reportes pesados
class ReporteService
{
    public function getPlanillaMensual(int $mes, int $gestion): Collection
    {
        $cacheKey = "planilla_{$gestion}_{$mes}";
        
        return Cache::remember($cacheKey, 3600, function () use ($mes, $gestion) {
            return DB::table('GestionSalarios')
                ->join('Contratos', 'GestionSalarios.IDContrato', '=', 'Contratos.IDContrato')
                ->join('Empleados', 'Contratos.IDEmpleado', '=', 'Empleados.IDEmpleado')
                ->where('Mes', $mes)
                ->where('Gestion', $gestion)
                ->select(['empleado_data', 'salary_data'])
                ->get();
        });
    }
}
```

### Export de Datos
```php
// Export service con múltiples formatos
class ExportService
{
    public function exportPlanillaPDF(int $mes, int $gestion): string
    {
        $data = $this->reporteService->getPlanillaMensual($mes, $gestion);
        return PDF::loadView('reports.planilla-pdf', compact('data', 'mes', 'gestion'))
                  ->download("planilla_{$gestion}_{$mes}.pdf");
    }
    
    public function exportPlanillaExcel(int $mes, int $gestion): BinaryFileResponse
    {
        return Excel::download(new PlanillaExport($mes, $gestion), 
                              "planilla_{$gestion}_{$mes}.xlsx");
    }
}
```

### Optimizaciones de Performance
- Queries con índices específicos para reportes
- Agregaciones pre-calculadas para KPIs frecuentes
- Lazy loading de datos pesados
- Paginación server-side para listados grandes

---

## Especificaciones Relacionadas

- `/Docs/specs/observability.md` - Métricas y dashboards
- `/Docs/specs/api-contracts.md` - Endpoints de reportes
- `/Docs/specs/ux-design.md` - UI patterns para dashboards

---

## Dependencias

- **Bloquea:** `fase6-0011` (Vacaciones UI), `fase8-0012` (Producción)
- **Bloqueado por:** `fase4-0005` (Planilla MVP), `fase3-0009` (Subsidios/Anticipos)

---

## Sub-Tareas

- [ ] Crear DashboardService con KPIs optimizados
- [ ] Implementar ReporteService con cache Redis
- [ ] Desarrollar ExportService para PDF/Excel/CSV
- [ ] Crear DashboardController con endpoints REST
- [ ] Implementar UI de dashboard con gráficos (Chart.js)
- [ ] Desarrollar interface de reportes con filtros avanzados
- [ ] Añadir sistema de cache invalidation inteligente
- [ ] Crear queries optimizadas con índices específicos
- [ ] Implementar tests de performance para reportes
- [ ] Añadir permisos granulares para reportes sensibles
- [ ] Documentar APIs de reportes en Swagger

---

## Comentarios y Discusión

**Owner:** [Placeholder - Full Stack Developer]
**Estimación:** 20-24 horas
**Sprint:** Sprint 5 (Semanas 11-12)

**Nota de performance**: Reportes con > 1000 registros deben usar jobs asíncronos y notificar por email cuando estén listos.