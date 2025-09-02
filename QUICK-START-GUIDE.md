# 🚀 Guía Rápida - Sistema RRHH YPFB

## ⚡ Inicio Rápido (5 minutos)

### 1. Levantar el sistema
```bash
docker-compose up -d
```

### 2. Verificar que todo funciona
```bash
# Health check
curl http://localhost:8081/api/health

# Debería devolver: {"status":"ok",...}
```

### 3. Login con credenciales de prueba
```bash
curl -X POST "http://localhost:8081/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@ypfb.gov.bo","password":"admin123"}'
```

### 4. Usar el token devuelto
```bash
# Reemplaza YOUR_TOKEN con el token del paso anterior
curl -X GET "http://localhost:8081/api/v1/empleados" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🔑 Credenciales de Prueba

| Usuario | Email | Password | Rol | Puede |
|---------|-------|----------|-----|--------|
| **Admin** | `admin@ypfb.gov.bo` | `admin123` | admin | Todo |
| **Manager** | `maria.rodriguez@ypfb.gov.bo` | `maria123` | manager | Crear/Editar/Ver |
| **User** | `carlos.mendoza@ypfb.gov.bo` | `carlos123` | user | Solo Ver |

---

## 📋 Endpoints Principales

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `GET /api/auth/profile` - Ver perfil 
- `POST /api/auth/logout` - Cerrar sesión

### Empleados (requiere autenticación)
- `GET /api/v1/empleados` - Listar todos
- `GET /api/v1/empleados/7` - Ver específico
- `POST /api/v1/empleados` - Crear (admin/manager)
- `PUT /api/v1/empleados/7` - Editar (admin/manager)
- `DELETE /api/v1/empleados/7` - Eliminar (admin/manager)

---

## 🛠️ Comandos Útiles para Desarrollo

```bash
# Ver logs
docker logs ds-db-rrhh-app-1 -f

# Ejecutar artisan
docker exec ds-db-rrhh-app-1 php artisan migrate

# Acceder a Laravel Tinker
docker exec ds-db-rrhh-app-1 php artisan tinker

# Ver base de datos
docker exec ds-db-rrhh-app-1 php artisan tinker --execute="echo App\Models\User::all();"

# Reiniciar aplicación
docker-compose restart app
```

---

## 🧪 Tests Rápidos

### Test 1: Sistema funcionando
```bash
curl http://localhost:8081/api/health
# Esperado: {"status":"ok"}
```

### Test 2: Login admin
```bash
curl -X POST "http://localhost:8081/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@ypfb.gov.bo","password":"admin123"}'
# Esperado: token en response
```

### Test 3: Acceso protegido sin token (debe fallar)
```bash
curl http://localhost:8081/api/v1/empleados
# Esperado: {"message":"Unauthenticated."}
```

### Test 4: Usuario sin permisos (debe fallar)
```bash
# Primero login como user
TOKEN=$(curl -s -X POST "http://localhost:8081/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"carlos.mendoza@ypfb.gov.bo","password":"carlos123"}' \
  | grep -o '"token":"[^"]*' | cut -d'"' -f4)

# Intentar crear empleado (debe fallar)
curl -X POST "http://localhost:8081/api/v1/empleados" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"ci":"test"}'
# Esperado: "No tienes permisos"
```

---

## 📊 Base de Datos

### Tablas principales
- `users` - Usuarios del sistema
- `empleados` - Información de empleados 
- `departamentos` - Departamentos/áreas
- `cargos` - Cargos/puestos
- `contratos` - Contratos laborales
- `personal_access_tokens` - Tokens de Sanctum

### Datos de prueba
- **8 empleados** con IDs 7-14
- **8 departamentos** (RRHH, TI, Operaciones, etc.)
- **13 cargos** (Gerentes, Desarrolladores, etc.)
- **6 usuarios** (admin, managers, users)

---

## 🚨 Solución de Problemas Comunes

### Error: "Connection refused"
```bash
# Verificar que los contenedores estén corriendo
docker-compose ps

# Si están parados, levantarlos
docker-compose up -d
```

### Error: "No se puede conectar a SQL Server"
```bash
# Verificar logs de SQL Server
docker logs ds-db-rrhh-sqlserver-1

# Reiniciar SQL Server si es necesario
docker-compose restart sqlserver
```

### Error: "File not found" en nginx
```bash
# Verificar que index.php existe
docker exec ds-db-rrhh-app-1 ls -la public/

# Si no existe, crearlo (ya debería estar)
# Reiniciar nginx
docker-compose restart nginx
```

### Error: "Class not found"
```bash
# Limpiar caché de Laravel
docker exec ds-db-rrhh-app-1 php artisan cache:clear
docker exec ds-db-rrhh-app-1 php artisan config:clear
docker exec ds-db-rrhh-app-1 php artisan route:clear
```

---

## 🎯 Próximos Pasos de Desarrollo

1. **Implementar módulo Contratos** usando Empleados como template
2. **Crear tests automatizados** (PHPUnit)
3. **Añadir más validaciones** de negocio
4. **Crear frontend** con Blade o Vue.js
5. **Implementar notificaciones** (emails, etc.)
6. **Añadir logs de auditoría** detallados
7. **Crear reportes** en PDF/Excel
8. **Optimizar queries** y performance

---

## 📚 Recursos Adicionales

- **Documentación completa:** `API-DOCUMENTATION.md`
- **Arquitectura:** `DIRECTORY-STRUCTURE.md`
- **Setup del equipo:** `TEAM-ONBOARDING.md`
- **Docker:** `docker-compose.yml`
- **Base de datos:** `database/migrations/`

---

**🔧 Desarrollado por:** Equipo YPFB  
**📅 Fecha:** 2025-09-02  
**🚀 Estado:** MVP Completado - Módulo Empleados + Autenticación