# Índice de Tickets - Sistema RRHH YPFB-Andina

## Resumen General
Sistema de tracking para todos los tickets del proyecto Sistema RRHH, organizados por fases y prioridades según el roadmap de 8 sprints.

## Estado General del Proyecto
- **Total Tickets**: 8 tickets críticos creados
- **Fases Cubiertas**: Sprint 0 (Fundación) → Sprint 8 (Producción)
- **Prioridad**: 6 tickets Alta/Crítica, 2 tickets Media

---

## Sprint 0: Fundación (Semanas 1-2)

### [fase0-0001-infraestructura-desarrollo.md](./fase0-0001-infraestructura-desarrollo.md)
- **Estado**: Abierto
- **Prioridad**: Alta
- **Descripción**: Configurar Docker Compose, SQL Server, Redis y entorno de desarrollo
- **Owner**: [DevOps Lead]
- **Estimación**: 8-12 horas

### [fase0-0002-datos-semilla.md](./fase0-0002-datos-semilla.md)
- **Estado**: Abierto  
- **Prioridad**: Alta
- **Descripción**: Seeders para 335 empleados, contratos, subsidios y datos maestros
- **Owner**: [Backend Developer]
- **Estimación**: 12-16 horas

### [fase0-0007-tailwind-setup.md](./fase0-0007-tailwind-setup.md)
- **Estado**: Abierto
- **Prioridad**: Media
- **Descripción**: Configurar TailwindCSS con tokens YPFB y sistema de componentes
- **Owner**: [Frontend Developer]
- **Estimación**: 8-12 horas

### [fase0-0008-ci-cd-pipeline.md](./fase0-0008-ci-cd-pipeline.md)
- **Estado**: Abierto
- **Prioridad**: Alta
- **Descripción**: Pipeline CI/CD con tests, linting, security y deployment
- **Owner**: [DevOps Engineer]
- **Estimación**: 12-16 horas

---

## Sprint 1: Módulo Empleados (Semanas 3-4)

### [fase1-0003-api-empleados.md](./fase1-0003-api-empleados.md)
- **Estado**: Abierto
- **Prioridad**: Alta
- **Descripción**: API REST completa para CRUD empleados con auditoría
- **Owner**: [Backend Developer]
- **Estimación**: 16-20 horas

---

## Sprint 2: Módulo Contratos (Semanas 5-6)

### [fase2-0004-api-contratos.md](./fase2-0004-api-contratos.md)
- **Estado**: Abierto
- **Prioridad**: Alta  
- **Descripción**: API contratos con validación de solapes y generación PDF
- **Owner**: [Backend Developer]
- **Estimación**: 20-24 horas

---

## Sprint 3: Subsidios y Anticipos (Semanas 7-8)

### [fase3-0009-subsidios-anticipos.md](./fase3-0009-subsidios-anticipos.md)
- **Estado**: Abierto
- **Prioridad**: Alta
- **Descripción**: Sistema completo de subsidios y anticipos con workflows
- **Owner**: [Backend + Frontend Developer]
- **Estimación**: 24-28 horas

---

## Sprint 4: Planilla MVP (Semanas 9-10)

### [fase4-0005-planilla-mvp.md](./fase4-0005-planilla-mvp.md)
- **Estado**: Abierto
- **Prioridad**: Crítica
- **Descripción**: Sistema central de generación de planilla con SPs e idempotencia
- **Owner**: [Senior Backend Developer]  
- **Estimación**: 28-32 horas

### [fase4-0006-triggers-sql.md](./fase4-0006-triggers-sql.md)
- **Estado**: Abierto
- **Prioridad**: Alta
- **Descripción**: Triggers críticos para validaciones e integridad de datos
- **Owner**: [DBA + Backend Developer]
- **Estimación**: 16-20 horas

---

## Sprint 5: Reportes y Dashboards (Semanas 11-12)

### [fase5-0010-reportes-dashboards.md](./fase5-0010-reportes-dashboards.md)
- **Estado**: Abierto
- **Prioridad**: Media
- **Descripción**: Dashboard ejecutivo con KPIs y reportes exportables
- **Owner**: [Full Stack Developer]
- **Estimación**: 20-24 horas

---

## Sprint 8: Producción y Optimización (Semanas 17-18)

### [fase8-0011-observabilidad-produccion.md](./fase8-0011-observabilidad-produccion.md)
- **Estado**: Abierto
- **Prioridad**: Crítica
- **Descripción**: Stack completo de observabilidad para producción
- **Owner**: [DevOps + SRE]
- **Estimación**: 24-30 horas

### [fase8-0012-migracion-datos.md](./fase8-0012-migracion-datos.md)
- **Estado**: Abierto
- **Prioridad**: Crítica
- **Descripción**: Migración de datos legacy con validación y rollback
- **Owner**: [DBA + Senior Developer]
- **Estimación**: 32-40 horas

---

## Métricas del Proyecto

### Distribución por Prioridad
- **Crítica**: 3 tickets (37.5%)
- **Alta**: 5 tickets (62.5%)
- **Media**: 2 tickets (25%)

### Estimación Total
- **Horas totales**: 184-218 horas
- **Recursos**: 6-8 desarrolladores
- **Duración**: 18 semanas (8 sprints × 2 semanas)

### Dependencies Chain
```
fase0-0001 → fase0-0002 → fase1-0003 → fase2-0004 → fase3-0009 → fase4-0005 → fase5-0010 → fase8-0011 → fase8-0012
```

---

## Próximos Tickets a Crear
- **UI Empleados**: Interfaz responsive para gestión empleados
- **UI Contratos**: Wizard de creación de contratos  
- **Vacaciones Module**: Solicitudes y aprobaciones de vacaciones
- **Security Hardening**: Penetration testing y hardening final
- **User Acceptance**: UAT con business users

---

## Notas del Proyecto
- **Última actualización**: 2025-08-31
- **Sprint actual**: Sprint 0 (Preparación)
- **Próximo milestone**: Infraestructura completa (Sprint 0)
- **Owner del proyecto**: [Placeholder - Project Manager]

---

## Referencias
- [Plan de Implementación](../Docs/plan.md)
- [Project Chapter Completo](../Docs/projectChapter.md)
- [Especificaciones Técnicas](../Docs/specs/)