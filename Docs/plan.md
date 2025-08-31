# Plan de Implementación - Sistema RRHH YPFB-Andina

## Resumen del Roadmap
Implementación del Sistema RRHH en 8 sprints de 2 semanas cada uno, priorizando módulos críticos y estableciendo bases sólidas para escalabilidad y mantenimiento.

## Sprint 0: Fundación (Semanas 1-2)
**Objetivo**: Establecer infraestructura base y herramientas de desarrollo

### Entregables
- Repositorio Git configurado con branching strategy
- Docker Compose para desarrollo local
- CI/CD pipeline base (lint, tests unitarios)
- Base de datos con esquema inicial y migraciones
- Seeding de datos maestros (departamentos, cargos, roles)

### Definition of Done
- [ ] Docker `docker-compose up` levanta entorno completo
- [ ] Pipeline CI ejecuta exitosamente en PRs
- [ ] Conexión a SQL Server funcional desde Laravel
- [ ] Seeds cargan datos maestros sin errores
- [ ] Health checks implementados en `/health`

### Dependencias
- Acceso a SQL Server 2019/2022
- Permisos para crear containers registry
- Configuración de secrets para CI/CD

### Prioridad: **CRÍTICA**

---

## Sprint 1: Módulo Empleados (Semanas 3-4)
**Objetivo**: CRUD completo de empleados con validaciones y auditoría

### Entregables
- Modelo Eloquent Empleado con relaciones
- Controladores y servicios para CRUD empleados
- Validaciones de CI boliviano y datos personales
- Interface UI para listado y detalle de empleados
- Trigger de auditoría para tabla Empleados
- Tests unitarios e integración

### Definition of Done
- [ ] API REST empleados funcional con validaciones
- [ ] UI responsive para gestión de empleados
- [ ] Auditoría registra cambios en AuditLog
- [ ] Tests cubren casos edge y validaciones
- [ ] Performance: listado < 2s para 335 empleados

### Dependencias
- Sprint 0 completado
- Datos de empleados para seeding disponibles

### Prioridad: **ALTA**

---

## Sprint 2: Módulo Contratos (Semanas 5-6)
**Objetivo**: Gestión de contratos con validación de solapes y workflows

### Entregables
- Modelo y repository para Contratos
- Función `fn_ValidarSolapeContrato` en DB
- Wizard UI para creación de contratos
- Estados de contrato y máquina de estados
- Validaciones de fechas y coherencia de datos
- Generación de documentos de contrato (PDF)

### Definition of Done
- [ ] No se permiten contratos superpuestos para mismo empleado
- [ ] Wizard guía creación paso a paso con validaciones
- [ ] PDF de contrato se genera automáticamente
- [ ] Estados de contrato funcionan correctamente
- [ ] Tests validan reglas de negocio críticas

### Dependencias
- Sprint 1 completado (empleados disponibles)
- Plantilla de contrato en PDF definida

### Prioridad: **ALTA**

---

## Sprint 3: Subsidios y Anticipos (Semanas 7-8)
**Objetivo**: Gestión de subsidios y anticipos con validaciones de negocio

### Entregables
- Modelos para Subsidios, TiposSubsidio, Anticipos
- Trigger `trg_Validar_Anticipo` para tope 50%
- Trigger `TRG_Subsidios_A_GestionSalarios` para propagación
- APIs para CRUD de subsidios y solicitud de anticipos
- Workflow de aprobación de anticipos
- UI para gestión de subsidios y anticipos

### Definition of Done
- [ ] Trigger impide anticipos > 50% haber básico
- [ ] Subsidios se propagan automáticamente a GestionSalarios
- [ ] Workflow de aprobación funcional
- [ ] Validaciones de negocio implementadas en múltiples capas
- [ ] Reports de subsidios y anticipos disponibles

### Dependencias
- Sprint 2 completado (contratos activos disponibles)
- Definición final de tipos de subsidios por RRHH

### Prioridad: **ALTA**

---

## Sprint 4: Planilla MVP (Semanas 9-10)
**Objetivo**: Sistema central de generación de planilla mensual

### Entregables
- `sp_GenerarPlanillaMensual` y `sp_CalcularSalarioMensual`
- Tabla `LogPlanilla` para tracking de procesos
- Service y controller para generación de planilla
- Application locks para exclusividad
- UI para preview y generación de planilla
- Queue jobs para procesamiento asíncrono
- Idempotencia en generación

### Definition of Done
- [ ] Planilla se genera correctamente para 335 empleados
- [ ] Proceso completo < 30s (P95)
- [ ] Idempotencia funciona con mismo IdempotencyKey
- [ ] UI muestra progreso en tiempo real
- [ ] Logs y auditoría completos
- [ ] Tests automatizados para cálculos críticos

### Dependencias
- Sprint 3 completado (subsidios y anticipos funcionando)
- Queue workers configurados (Redis)

### Prioridad: **CRÍTICA**

---

## Sprint 5: Reportes y Dashboards (Semanas 11-12)
**Objetivo**: Dashboards operativos y reportes básicos

### Entregables
- Dashboard principal con KPIs
- Reportes de planilla en PDF/Excel
- Queries optimizadas para reporting
- Cache de reportes pesados (Redis)
- Roles y permisos para reportes
- Export de datos en múltiples formatos

### Definition of Done
- [ ] Dashboard carga < 3s con datos agregados
- [ ] Reportes se generan sin timeouts
- [ ] Cache reduce tiempo de carga en 50%
- [ ] Exports funcionan para datasets grandes
- [ ] Permisos de reportes aplicados correctamente

### Dependencias
- Sprint 4 completado (datos de planilla disponibles)
- Definición de KPIs y métricas de negocio

### Prioridad: **MEDIA**

---

## Sprint 6: Vacaciones y Evaluaciones (Semanas 13-14)
**Objetivo**: Módulos complementarios de gestión de talento

### Entregables
- Modelo para SolicitudesVacaciones y Evaluaciones
- Función `fn_CalcularSaldoVacaciones`
- Workflow de aprobación de vacaciones
- Sistema de evaluaciones anuales
- Notificaciones automáticas a jefes
- Calendarios de vacaciones

### Definition of Done
- [ ] Saldos de vacaciones se calculan correctamente
- [ ] Workflow de aprobación funcional
- [ ] Notificaciones llegan a jefes apropiados
- [ ] Evaluaciones se registran y consultan
- [ ] Calendarios muestran conflictos de vacaciones

### Dependencias
- Sprints 1-2 completados (empleados y contratos)
- Definición de políticas de vacaciones

### Prioridad: **MEDIA**

---

## Sprint 7: Afiliaciones y Documentos (Semanas 15-16)
**Objetivo**: Gestión de afiliaciones y documentos digitales

### Entregables
- Módulo de Afiliaciones (AFP, Seguro, etc.)
- Sistema de gestión de documentos con S3/MinIO
- Upload y download seguro de archivos
- Versionado de documentos
- OCR básico para documentos escaneados
- Reportes de afiliaciones

### Definition of Done
- [ ] Documentos se almacenan en object storage
- [ ] Referencias en tabla Documentos funcionan
- [ ] Upload/download con validaciones de seguridad
- [ ] Afiliaciones vinculadas a empleados
- [ ] Versionado de documentos implementado

### Dependencias
- Storage S3-compatible configurado
- Políticas de seguridad para documentos

### Prioridad: **BAJA**

---

## Sprint 8: Producción y Optimización (Semanas 17-18)
**Objetivo**: Preparación para producción y optimizaciones finales

### Entregables
- Observabilidad completa (métricas, logs, alertas)
- Performance tuning y optimización de queries
- Security hardening y penetration testing
- Backup/restore procedures automatizados
- Runbooks operativos
- User acceptance testing
- Go-live preparation

### Definition of Done
- [ ] Métricas exportadas a Prometheus
- [ ] Alertas configuradas para todos los servicios críticos
- [ ] Performance targets alcanzados (P95 < 3s)
- [ ] Security audit passed sin vulnerabilidades críticas
- [ ] Backup/restore procedures validados
- [ ] UAT completado con business users
- [ ] Runbooks documentados y probados

### Dependencias
- Todos los sprints anteriores completados
- Stack de observabilidad configurado
- Security team disponible para auditoría

### Prioridad: **CRÍTICA**

---

## Riesgos y Mitigaciones

### Riesgos Técnicos
- **SQL Server Performance**: Mitigación - prototipar con 335 empleados reales
- **Concurrencia en Planilla**: Mitigación - implementar application locks temprano
- **Data Migration**: Mitigación - testing exhaustivo con datos enmascarados

### Riesgos de Proyecto
- **Cambios de Requirements**: Mitigación - sprints cortos y feedback frecuente
- **Disponibilidad de DBA**: Mitigación - documentar objetos SQL críticos
- **Testing Environment**: Mitigación - containers para replicar producción

## Métricas de Éxito

### Technical KPIs
- Response time P95 < 3 segundos
- Uptime ≥ 99.9%
- Test coverage ≥ 70%
- Security vulnerabilities = 0 críticas

### Business KPIs
- Tiempo de generación planilla < 30 segundos
- Accuracy en cálculos = 99.9%
- User adoption ≥ 95% en 3 meses
- Reducción tiempo procesos RRHH en 50%

## Referencias al Documento Canónico
Este roadmap se basa en las secciones 15, 37 y 56 del [Project Chapter](../projectChapter.md). Para detalles específicos de implementación, arquitectura y criterios técnicos, consultar el documento principal.