# Especificación de Observabilidad - Sistema RRHH YPFB-Andina

## Propósito
Definir la estrategia de observabilidad completa del Sistema RRHH, incluyendo métricas, logs, tracing y alertas para garantizar operación confiable y troubleshooting eficiente.

## Alcance
- Métricas de aplicación y base de datos
- Logs estructurados con correlación de traces
- APM y distributed tracing
- Dashboards operativos y de negocio
- Alertas automatizadas y runbooks

## Métricas (Prometheus)

### Métricas de Aplicación
```
# API Performance
api_request_duration_seconds{method, endpoint, status}
api_request_errors_total{endpoint, error_code}
api_request_rate{endpoint}

# Planilla (Crítico)
planilla_generation_duration_seconds{mes, gestion}
planilla_generation_success_total{mes, gestion}
planilla_generation_fail_total{mes, gestion, error_type}
planilla_contracts_processed_total{planilla_id}

# Queue/Workers
queue_jobs_processed_total{queue, status}
queue_jobs_failed_total{queue, error_type}
queue_job_duration_seconds{queue, job_type}

# Cache
redis_cache_hits_total{cache_type}
redis_cache_misses_total{cache_type}
redis_memory_usage_bytes
```

### Métricas de Base de Datos
```
# Performance
db_query_duration_seconds{query_type}
db_deadlocks_total
db_long_running_queries_total{threshold="5s"}
db_connections_active
db_connections_max

# Storage
db_size_bytes{database}
db_log_size_bytes{database}
db_backup_duration_seconds{backup_type}
db_backup_success_total{backup_type}
```

## Logs Estructurados

### Formato JSON Estándar
```json
{
  "timestamp": "2025-08-31T10:30:45.123Z",
  "level": "INFO",
  "service": "rrhh-api",
  "env": "production",
  "user_id": 123,
  "trace_id": "550e8400-e29b-41d4-a716-446655440000",
  "route": "POST /api/v1/planilla/generar",
  "duration_ms": 25000,
  "message": "Planilla generada exitosamente",
  "meta": {
    "planilla_id": 456,
    "contracts_processed": 335,
    "mes": 8,
    "gestion": 2025
  }
}
```

### Niveles de Log
- **DEBUG**: Información detallada para desarrollo
- **INFO**: Eventos de negocio importantes (planilla generada, contrato creado)
- **WARN**: Condiciones recuperables (reintentos, degradación)
- **ERROR**: Fallos que requieren atención inmediata
- **FATAL**: Errores críticos que impiden operación

### Contexto de Correlación
- **trace_id**: UUID único por request para correlación completa
- **user_id**: Identificador del usuario para auditoría
- **session_id**: Para tracking de sesiones largas
- **correlation_id**: Para operaciones distribuidas

## Distributed Tracing

### Instrumentación APM
```php
// Laravel - Instrumentación de servicios críticos
class PlanillaService
{
    public function generarPlanillaMensual(int $mes, int $gestion): array
    {
        $traceId = request()->header('X-Trace-Id') ?? Str::uuid();
        
        \Log::info('Iniciando generación de planilla', [
            'trace_id' => $traceId,
            'mes' => $mes,
            'gestion' => $gestion,
            'user_id' => auth()->id()
        ]);
        
        $startTime = microtime(true);
        
        try {
            // Lógica de negocio...
            $result = $this->executePlanillaGeneration($mes, $gestion, $traceId);
            
            $duration = (microtime(true) - $startTime) * 1000;
            
            \Log::info('Planilla generada exitosamente', [
                'trace_id' => $traceId,
                'duration_ms' => $duration,
                'planilla_id' => $result['planilla_id']
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            \Log::error('Error generando planilla', [
                'trace_id' => $traceId,
                'error' => $e->getMessage(),
                'duration_ms' => (microtime(true) - $startTime) * 1000
            ]);
            throw $e;
        }
    }
}
```

## Dashboards Operativos

### Dashboard Principal (Grafana)
- **Uptime**: Disponibilidad de servicios (target 99.9%)
- **Performance**: P50, P95, P99 de response times
- **Error Rate**: 4xx/5xx rates por endpoint
- **Throughput**: Requests per second
- **Database**: Query performance, deadlocks, connections

### Dashboard de Negocio
- **Planillas**: Generaciones exitosas vs fallidas por mes
- **Usuarios Activos**: DAU/MAU del sistema
- **Operaciones**: Contratos creados, anticipos procesados
- **Auditoría**: Volumen de cambios por módulo

## Alertas Automatizadas

### Alertas Críticas (PagerDuty/Teams)
```yaml
# Alertas de alta prioridad
alerts:
  - name: "API P95 Alto"
    condition: "api_request_duration_seconds{quantile='0.95'} > 3"
    for: "15m"
    severity: "critical"
    
  - name: "Planilla Generation Failed"
    condition: "planilla_generation_fail_total > 0"
    for: "1m"
    severity: "critical"
    
  - name: "Database Deadlocks"
    condition: "rate(db_deadlocks_total[5m]) > 0.1"
    for: "10m"
    severity: "warning"
    
  - name: "Disk Space Low"
    condition: "node_filesystem_free_bytes / node_filesystem_size_bytes < 0.1"
    for: "5m"
    severity: "critical"
```

### Alertas de Negocio
- **Planilla No Generada**: Si no se genera planilla en fecha esperada
- **Anticipos Excesivos**: Si anticipos superan umbral mensual
- **Usuarios Inactivos**: Si admin no accede en 7 días

## Retención de Datos

### Políticas de Retención
- **Logs de aplicación**: 90 días en storage inmediato, 1 año en archive
- **AuditLog**: 5 años según regulaciones
- **Métricas**: 1 año con resolución completa, 3 años agregado
- **Traces**: 7 días para troubleshooting inmediato

### Archivado Automático
```sql
-- Job mensual: Archive AuditLog older than 1 year
INSERT INTO AuditLog_Archive 
SELECT * FROM AuditLog 
WHERE CreatedAt < DATEADD(YEAR, -1, GETDATE());

DELETE FROM AuditLog 
WHERE CreatedAt < DATEADD(YEAR, -1, GETDATE());
```

## Dependencias
- Prometheus para métricas y alertas
- Grafana para dashboards y visualización
- Elastic APM o Application Insights para tracing
- Loki o Elasticsearch para logs centralizados
- PagerDuty/Teams para notificaciones

## Criterios de Aceptación
- [ ] Métricas exportadas correctamente a Prometheus
- [ ] Logs estructurados JSON con trace_id en todos los servicios
- [ ] Dashboards operativos funcionales en Grafana
- [ ] Alertas críticas configuradas con runbooks
- [ ] APM tracing implementado en operaciones críticas
- [ ] Correlación trace_id funciona end-to-end
- [ ] Retención de datos configurada según políticas
- [ ] Performance baseline establecido (P95 < 3s)
- [ ] Archivado automático funcionando
- [ ] Integration con herramientas de incident management

## Referencias al Documento Canónico
Este documento se basa en las secciones 22, 52 y 56 del [Project Chapter](../projectChapter.md). Para configuraciones específicas de Prometheus, queries de performance y runbooks operativos, consultar el documento principal.

**Supuestos:**
- Stack de observabilidad (Prometheus/Grafana) disponible
- Permisos para configurar alertas y dashboards
- APM tool seleccionado e integrado