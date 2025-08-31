# Ticket: Configurar Infraestructura de Desarrollo

- **ID del Ticket:** `fase0-0001`
- **Fase:** `Sprint 0: Fundación`
- **Estado:** `Abierto`
- **Prioridad:** `Alta`

---

## Descripción

Establecer la infraestructura completa de desarrollo incluyendo Docker Compose, configuración de base de datos SQL Server, Redis para cache/colas y servicios complementarios. Esta es la base fundamental para que el equipo pueda desarrollar localmente.

---

## Criterios de Aceptación

- [ ] Docker Compose levanta todos los servicios con `docker-compose up -d`
- [ ] SQL Server 2019/2022 accesible desde Laravel con drivers sqlsrv
- [ ] Redis funcional para cache y queue workers
- [ ] MailHog configurado para testing de emails en desarrollo
- [ ] Variables de entorno documentadas en `.env.example`
- [ ] Health checks implementados para todos los servicios
- [ ] Documentación en README para setup inicial < 15 minutos

---

## Detalles Técnicos y Notas de Implementación

### Docker Compose Services Requeridos
```yaml
services:
  app:
    build: .
    volumes: ["./:/var/www/html"]
    environment: ["DB_HOST=sqlserver"]
  
  sqlserver:
    image: "mcr.microsoft.com/mssql/server:2019-latest"
    environment:
      SA_PASSWORD: "DevPassword123!"
      ACCEPT_EULA: "Y"
    ports: ["1433:1433"]
  
  redis:
    image: "redis:7-alpine"
    ports: ["6379:6379"]
  
  mailhog:
    image: "mailhog/mailhog"
    ports: ["8025:8025"]
```

### Configuración Laravel
- Drivers PHP: pdo_sqlsrv, sqlsrv instalados en imagen Docker
- `.env` configurado para servicios locales
- Cache y queue drivers apuntando a Redis

---

## Especificaciones Relacionadas

- `/Docs/specs/architecture.md` - Arquitectura general del sistema
- `/Docs/specs/ci-cd.md` - Pipeline de CI/CD

---

## Dependencias

- **Bloquea:** `fase0-0002` (Datos semilla), `fase1-0003` (API Empleados)
- **Bloqueado por:** Ninguno (ticket inicial)

---

## Sub-Tareas

- [ ] Crear Dockerfile multi-stage para aplicación Laravel
- [ ] Configurar docker-compose.yml con todos los servicios
- [ ] Instalar y configurar drivers SQL Server en contenedor PHP
- [ ] Configurar `.env.example` con variables requeridas
- [ ] Implementar health checks para cada servicio
- [ ] Crear scripts de inicio y verificación
- [ ] Documentar proceso de setup en README

---

## Comentarios y Discusión

**Owner:** [Placeholder - DevOps Lead]
**Estimación:** 8-12 horas
**Sprint:** Sprint 0 (Semanas 1-2)