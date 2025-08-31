# Ticket: Implementar Observabilidad para Producción

- **ID del Ticket:** `fase8-0011`
- **Fase:** `Sprint 8: Producción y Optimización`
- **Estado:** `Abierto`
- **Prioridad:** `Crítica`

---

## Descripción

Implementar stack completo de observabilidad incluyendo métricas Prometheus, logs estructurados, distributed tracing y alertas automatizadas para garantizar operación confiable en producción.

---

## Criterios de Aceptación

- [ ] Métricas exportadas a Prometheus (API, DB, workers, planilla)
- [ ] Logs estructurados JSON con trace_id correlation
- [ ] Dashboards Grafana operativos y de negocio funcionando
- [ ] Alertas críticas configuradas con PagerDuty/Teams
- [ ] APM tracing implementado para operaciones críticas
- [ ] Performance baseline establecido (P95 < 3s)
- [ ] Runbooks asociados a cada alerta
- [ ] Health checks completos en todos los servicios
- [ ] Retention policies configuradas según regulaciones

---

## Detalles Técnicos y Notas de Implementación

### Métricas Críticas (Prometheus)
```php
// Laravel - Instrumentación de métricas
use Prometheus\CollectorRegistry;

class MetricsService
{
    private $registry;
    
    public function recordApiRequest(string $endpoint, int $duration, int $status): void
    {
        $histogram = $this->registry->getOrRegisterHistogram(
            'api_request_duration_seconds',
            'API request duration',
            ['endpoint', 'status']
        );
        
        $histogram->observe($duration / 1000, [$endpoint, $status]);
    }
    
    public function recordPlanillaGeneration(int $mes, int $gestion, int $duration, bool $success): void
    {
        $counter = $this->registry->getOrRegisterCounter(
            'planilla_generation_total',
            'Planilla generations',
            ['mes', 'gestion', 'status']
        );
        
        $counter->inc([$mes, $gestion, $success ? 'success' : 'failed']);
    }
}
```

### Structured Logging
```php
// Formato JSON estructurado
Log::info('Planilla generada exitosamente', [
    'trace_id' => request()->header('X-Trace-Id'),
    'user_id' => auth()->id(),
    'planilla_id' => $planillaId,
    'mes' => $mes,
    'gestion' => $gestion,
    'duration_ms' => $duration,
    'contracts_processed' => $contractsCount
]);
```

### Alertas Críticas
```yaml
# prometheus/alerts.yml
groups:
  - name: rrhh-critical
    rules:
      - alert: APIHighLatency
        expr: histogram_quantile(0.95, rate(api_request_duration_seconds_bucket[5m])) > 3
        for: 15m
        labels:
          severity: critical
        annotations:
          summary: "API P95 latency is high"
          runbook: "/docs/runbooks.md#api-performance"
          
      - alert: PlanillaGenerationFailed
        expr: increase(planilla_generation_total{status="failed"}[1h]) > 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "Planilla generation failed"
          runbook: "/docs/runbooks.md#planilla-troubleshooting"
```

### Dashboards Grafana
- **Dashboard Operativo**: Uptime, latency, error rates, resource usage
- **Dashboard de Negocio**: Planillas generadas, usuarios activos, operaciones por módulo
- **Dashboard de Base de Datos**: Query performance, deadlocks, connections

### Distributed Tracing
- Propagación de X-Trace-Id desde frontend a backend a DB
- Instrumentación en controllers, services y repository layer
- Correlation en logs y métricas

---

## Especificaciones Relacionadas

- `/Docs/specs/observability.md` - Métricas y dashboards detallados
- `/Docs/specs/runbooks.md` - Procedimientos operativos

---

## Dependencias

- **Bloquea:** `fase8-0012` (Go-live preparación)
- **Bloqueado por:** `fase4-0005` (Planilla MVP), `fase5-0010` (Reportes)

---

## Sub-Tareas

- [ ] Instalar y configurar Prometheus server
- [ ] Implementar métricas custom en Laravel app
- [ ] Configurar Grafana con dashboards operativos
- [ ] Implementar structured logging con trace correlation
- [ ] Configurar alertas críticas con runbooks
- [ ] Implementar APM tracing (Elastic APM o New Relic)
- [ ] Configurar log aggregation (Loki o ELK)
- [ ] Crear health check endpoints completos
- [ ] Implementar retention policies para métricas y logs
- [ ] Añadir monitoring de jobs y workers
- [ ] Configurar alertas de negocio (planilla no generada)

---

## Comentarios y Discusión

**Owner:** [Placeholder - DevOps + SRE]
**Estimación:** 24-30 horas
**Sprint:** Sprint 8 (Semanas 17-18)

**Nota crítica**: Observabilidad es requisito obligatorio para go-live. Sin métricas y alertas adecuadas no se puede garantizar SLA de 99.9% uptime.