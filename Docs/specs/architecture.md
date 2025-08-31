# Especificación de Arquitectura - Sistema RRHH YPFB-Andina

## Propósito
Definir la arquitectura técnica del Sistema RRHH para YPFB-Andina, estableciendo patrones, tecnologías y principios que guiarán la implementación de todos los módulos del sistema.

## Alcance
- Arquitectura de 3 capas (UI → Controller → Service → Repository → DB)
- Stack tecnológico: Laravel 11 + PHP 8.3 + SQL Server 2019/2022
- Patrones de diseño y convenciones de código
- Estrategias de cache, colas y almacenamiento
- Observabilidad y monitoreo

## Arquitectura General

### Patrón de 3 Capas
```
UI (Blade/React) → Controller → Service → Repository → Database
```

**Responsabilidades por capa:**
- **UI**: Presentación, validaciones client-side, UX/responsive
- **Controller**: Routing, autenticación, validación request, serialización response
- **Service**: Lógica de negocio, orquestación de transacciones, llamadas a SPs
- **Repository**: Acceso a datos, queries Eloquent, mapeo DTO
- **Database**: Stored Procedures, triggers, funciones, constraints

### Stack Tecnológico Definido
- **Backend**: Laravel 11 (PHP 8.3) con drivers pdo_sqlsrv/sqlsrv
- **Frontend**: Vite + React (SPA) o Blade + Alpine.js + TailwindCSS
- **Base de Datos**: SQL Server 2019/2022 con objetos críticos (SPs, triggers, TVFs)
- **Cache/Colas**: Redis 6/7 para sessions, cache y queue workers
- **Storage**: S3/MinIO para documentos con referencias en tabla Documentos
- **Contenedores**: Docker multi-stage, php-fpm + Nginx

### Componentes de Infraestructura
```
[NGINX LB] → [App replicas (PHP-FPM)] → [SQL Server Primary]
                           ↓
                      [Redis Cache/Queue]
                           ↓
                    [Worker replicas]
                           ↓
                      [S3/MinIO Storage]
```

## Endpoints y Contratos API

### Patrón de API REST
- Base URL: `/api/v1/`
- Autenticación: Laravel Sanctum (Bearer tokens)
- Formato de respuesta estándar con códigos normalizados
- Idempotencia en endpoints mutativos críticos

### Ejemplo: Generación de Planilla
```http
POST /api/v1/planilla/generar
Authorization: Bearer <token>
Idempotency-Key: <uuid-v4>

{
  "mes": 8,
  "gestion": 2025
}
```

**Response Success (200):**
```json
{
  "success": true,
  "code": 0,
  "data": {
    "planilla_id": 123,
    "status": "Completado"
  },
  "message": "Planilla 8/2025 generada exitosamente"
}
```

## Dependencias Críticas

### Integración con Base de Datos
- **Eloquent Models**: Para operaciones CRUD estándar
- **Stored Procedures**: Para cálculos set-based y operaciones críticas
- **Triggers**: Para validaciones de integridad e invariantes de negocio
- **Application Locks**: Para exclusividad en procesos como generación de planilla

### Patrones de Concurrencia
- **Optimista**: rowversion/timestamps para ediciones UI
- **Pesimista**: sp_getapplock para procesos exclusivos (planilla mensual)
- **Transacciones**: BEGIN TRY/CATCH con rollback automático

### Gestión de Archivos
- Almacenamiento en S3/MinIO con referencias en tabla `Documentos`
- Validación de tipos MIME y límites de tamaño
- Versionado y auditoría de documentos críticos

## Criterios de Aceptación

### Performance y Escalabilidad
- [ ] Endpoints P95 < 3 segundos para operaciones estándar
- [ ] Generación de planilla < 30 segundos para 335 empleados
- [ ] Sistema soporta hasta 2,500 empleados sin degradación

### Seguridad
- [ ] Autenticación JWT/Sanctum implementada
- [ ] RBAC con policies granulares por módulo
- [ ] Validaciones en múltiples capas (UI, API, DB)
- [ ] Auditoría completa en tabla AuditLog

### Disponibilidad y Observabilidad
- [ ] Healthchecks en todos los servicios
- [ ] Logs estructurados JSON con trace_id
- [ ] Métricas exportadas a Prometheus
- [ ] Alertas configuradas para P95 y errores críticos

### Mantenibilidad
- [ ] Código sigue convenciones PSR-12 y Laravel
- [ ] Tests automatizados con cobertura ≥70%
- [ ] Documentación API actualizada (OpenAPI/Swagger)
- [ ] Runbooks operativos para despliegues

## Referencias al Documento Canónico
Este documento es un extracto de las secciones 4, 12, 22 y 25 del [Project Chapter](../projectChapter.md). Para detalles completos sobre diagramas, ejemplos de código y configuraciones específicas, consultar el documento principal.

**Supuestos:**
- SQL Server 2019/2022 disponible con permisos DBA para crear objetos
- Redis disponible para cache y colas
- Storage S3-compatible para documentos