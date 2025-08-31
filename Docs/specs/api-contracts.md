# Especificación de Contratos API - Sistema RRHH YPFB-Andina

## Propósito
Definir los contratos API REST para todos los módulos del Sistema RRHH, estableciendo estándares de comunicación, formatos de datos y códigos de respuesta normalizados.

## Alcance
- Endpoints REST para módulos: Empleados, Contratos, Planilla, Subsidios, Anticipos, Vacaciones
- Esquemas de request/response estándar
- Códigos de error normalizados
- Headers de seguridad e idempotencia
- Documentación OpenAPI/Swagger

## Estándares de API

### Base URL y Versionado
- **Base URL**: `/api/v1/`
- **Autenticación**: `Authorization: Bearer <token>` (Laravel Sanctum)
- **Content-Type**: `application/json`
- **Idempotencia**: `Idempotency-Key: <uuid-v4>` para operaciones mutativas

### Formato de Respuesta Estándar

**Success Response:**
```json
{
  "success": true,
  "code": 0,
  "data": { },
  "message": "Operación exitosa"
}
```

**Error Response:**
```json
{
  "success": false,
  "code": -1,
  "error": "Mensaje para usuario",
  "details": {
    "field": "error específico"
  }
}
```

## Endpoints Críticos

### Módulo Empleados
```http
GET    /api/v1/empleados              # Lista paginada con filtros
POST   /api/v1/empleados              # Crear empleado
GET    /api/v1/empleados/{id}         # Detalle empleado
PUT    /api/v1/empleados/{id}         # Actualizar empleado
DELETE /api/v1/empleados/{id}         # Soft delete empleado
```

**Ejemplo Request POST /api/v1/empleados:**
```json
{
  "ci": "12345678",
  "nombres": "Juan Carlos",
  "apellido_paterno": "Pérez",
  "apellido_materno": "González",
  "fecha_nacimiento": "1985-03-15",
  "email": "juan.perez@ypfb.gov.bo",
  "telefono": "591-70123456"
}
```

### Módulo Contratos
```http
GET    /api/v1/contratos              # Lista contratos
POST   /api/v1/contratos              # Crear contrato (valida solapes)
GET    /api/v1/contratos/{id}         # Detalle contrato
PUT    /api/v1/contratos/{id}         # Actualizar contrato
```

**Request POST /api/v1/contratos:**
```json
{
  "id_empleado": 123,
  "numero_contrato": "CT-2025-001",
  "fecha_inicio": "2025-01-01",
  "fecha_fin": "2025-12-31",
  "haber_basico": 8000.00,
  "id_departamento": 5,
  "id_cargo": 12,
  "observaciones": "Contrato temporal"
}
```

### Módulo Planilla (Crítico)
```http
POST   /api/v1/planilla/generar       # Generar planilla mensual
GET    /api/v1/planilla/status/{id}   # Estado de generación
GET    /api/v1/planilla/{mes}/{gestion} # Ver planilla generada
```

**Request POST /api/v1/planilla/generar:**
```json
{
  "mes": 8,
  "gestion": 2025,
  "idempotency_key": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Response 202 Accepted:**
```json
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

### Módulo Subsidios y Anticipos
```http
GET    /api/v1/subsidios              # Lista subsidios
POST   /api/v1/subsidios              # Crear subsidio
GET    /api/v1/anticipos              # Lista anticipos por empleado
POST   /api/v1/anticipos              # Solicitar anticipo (valida 50% tope)
PUT    /api/v1/anticipos/{id}/aprobar # Aprobar/rechazar anticipo
```

## Códigos de Error Normalizados

| Código | Descripción | HTTP Status | Acción Recomendada |
|--------|-------------|-------------|-------------------|
| 0 | Éxito | 200/201/202 | Continuar |
| -1 | Parámetro inválido | 400 | Verificar datos enviados |
| -2 | Recurso no encontrado | 404 | Verificar ID existe |
| -3 | Conflicto/Proceso en curso | 409 | Reintentar en 30s |
| -4 | Permiso denegado | 403 | Verificar autorización |
| -5 | Límite de tasa excedido | 429 | Esperar X-RateLimit-Reset |
| -99 | Error interno | 500 | Revisar logs del servidor |

## Headers de Seguridad

### Request Headers
- `Authorization: Bearer <token>` (obligatorio)
- `X-Trace-Id: <uuid>` (recomendado para tracing)
- `Idempotency-Key: <uuid>` (obligatorio en operaciones mutativas críticas)
- `If-Match: <rowversion>` (para updates con concurrencia optimista)

### Response Headers
- `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- `X-Trace-Id` (eco del request o generado)
- `ETag: <rowversion>` (para recursos versionados)

## Dependencias
- Laravel Sanctum para autenticación API
- Middleware de throttling configurado
- Policies y Gates para autorización granular
- Stored Procedures para operaciones críticas (sp_GenerarPlanillaMensual)
- Tabla LogPlanilla para tracking de operaciones largas

## Criterios de Aceptación
- [ ] Todos los endpoints respetan formato de respuesta estándar
- [ ] Códigos de error normalizados implementados
- [ ] Documentación OpenAPI/Swagger generada automáticamente
- [ ] Rate limiting configurado en endpoints críticos
- [ ] Idempotencia implementada en operaciones mutativas
- [ ] Tests de integración para endpoints críticos con cobertura ≥70%
- [ ] Response time P95 < 3 segundos para operaciones estándar
- [ ] Operaciones largas (planilla) usan patrón 202 Accepted + polling

## Referencias al Documento Canónico
Basado en las secciones 4.2, 17 y 27.0 del [Project Chapter](../projectChapter.md). Para ejemplos completos de OpenAPI y detalles de implementación, consultar el documento principal.

**Supuestos:**
- Laravel Sanctum será usado para autenticación API
- Redis disponible para rate limiting y cache
- Logs estructurados JSON implementados para auditoría