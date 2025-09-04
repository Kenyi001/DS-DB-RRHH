# 📖 API Documentation - Sistema RRHH YPFB

## 🔗 Base URL
```
http://localhost:8081/api
```

## 🔐 Autenticación

Este API utiliza **Laravel Sanctum** con tokens Bearer para autenticación.

### Credenciales de Prueba

| Rol | Email | Password | Descripción |
|-----|-------|----------|-------------|
| **Admin** | `admin@ypfb.gov.bo` | `admin123` | Acceso completo al sistema |
| **Manager** | `maria.rodriguez@ypfb.gov.bo` | `maria123` | Gestión de RRHH |
| **User** | `carlos.mendoza@ypfb.gov.bo` | `carlos123` | Usuario regular |
| **User** | `ana.vargas@ypfb.gov.bo` | `ana123` | Usuario regular |
| **User** | `luis.torrez@ypfb.gov.bo` | `luis123` | Usuario regular |

---

## 🔓 Endpoints Públicos (Sin autenticación)

### 1. Health Check
```http
GET /health
```
**Respuesta:**
```json
{
  "status": "ok",
  "timestamp": "2025-09-02T02:30:00.000000Z",
  "services": {
    "database": "connected",
    "redis": "connected"
  }
}
```

### 2. Registro de Usuario
```http
POST /auth/register
Content-Type: application/json
```
**Body:**
```json
{
  "name": "Juan Pérez",
  "email": "juan.perez@ypfb.gov.bo",
  "password": "password123",
  "password_confirmation": "password123",
  "empleado_id": 7,
  "role": "user"
}
```

### 3. Login
```http
POST /auth/login
Content-Type: application/json
```
**Body:**
```json
{
  "email": "admin@ypfb.gov.bo",
  "password": "admin123"
}
```
**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "id": 1,
      "name": "Administrador Sistema",
      "email": "admin@ypfb.gov.bo",
      "role": "admin",
      "empleado_id": "7",
      "empleado": {
        "nombres": "Juan Carlos",
        "apellido_paterno": "González",
        "apellido_materno": "Pérez",
        "codigo_empleado": "YPFB001"
      }
    },
    "token": "2|o452VmKynJHRACiQrzkqjeIQwcAFqs3U4eqG5jfK909e859f",
    "token_type": "Bearer"
  }
}
```

---

## 🔒 Endpoints Protegidos (Requieren autenticación)

**Cabecera requerida:**
```
Authorization: Bearer <token>
```

### Gestión de Sesión

#### Logout
```http
POST /auth/logout
Authorization: Bearer <token>
```

#### Perfil de Usuario
```http
GET /auth/profile
Authorization: Bearer <token>
```

#### Cambiar Contraseña
```http
POST /auth/change-password
Content-Type: application/json
Authorization: Bearer <token>
```
**Body:**
```json
{
  "current_password": "password123",
  "new_password": "newpassword456",
  "new_password_confirmation": "newpassword456"
}
```

---

## 👥 Módulo Empleados

### Permisos por Rol:
- **👑 Admin/Manager**: CRUD completo (crear, leer, actualizar, eliminar)
- **👤 User**: Solo lectura (listar, ver detalles)

### Endpoints

#### Listar Empleados
```http
GET /v1/empleados
Authorization: Bearer <token>
```
**Parámetros opcionales:**
- `page=1` - Número de página
- `per_page=15` - Elementos por página
- `search=Juan` - Buscar por nombre/apellido/CI
- `genero=M` - Filtrar por género (M/F)
- `estado=Activo` - Filtrar por estado
- `ciudad=La Paz` - Filtrar por ciudad

**Ejemplo:**
```http
GET /v1/empleados?genero=F&estado=Activo&page=1&per_page=5
```

#### Ver Empleado
```http
GET /v1/empleados/{id}
Authorization: Bearer <token>
```

#### Crear Empleado (Solo Admin/Manager)
```http
POST /v1/empleados
Content-Type: application/json
Authorization: Bearer <token>
```
**Body:**
```json
{
  "ci": "1234567",
  "nombres": "Pedro",
  "apellido_paterno": "Fernández", 
  "apellido_materno": "Castro",
  "fecha_nacimiento": "1990-05-15",
  "genero": "M",
  "estado_civil": "Soltero",
  "telefono": "2-2987654",
  "celular": "79876543",
  "email": "pedro.fernandez@ypfb.gov.bo",
  "direccion": "Calle Test #999, Zona Prueba",
  "ciudad": "La Paz",
  "codigo_empleado": "YPFB009",
  "fecha_ingreso": "2025-01-01",
  "nacionalidad": "Boliviana"
}
```

#### Actualizar Empleado (Solo Admin/Manager)
```http
PUT /v1/empleados/{id}
Content-Type: application/json
Authorization: Bearer <token>
```

#### Eliminar Empleado (Solo Admin/Manager)
```http
DELETE /v1/empleados/{id}
Authorization: Bearer <token>
```

---

## 🧪 Ejemplos de Uso con cURL

### 1. Login y obtener token
```bash
curl -X POST "http://localhost:8081/api/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@ypfb.gov.bo",
    "password": "admin123"
  }'
```

### 2. Listar empleados con filtros
```bash
curl -X GET "http://localhost:8081/api/v1/empleados?genero=F&estado=Activo" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 3. Ver empleado específico
```bash
curl -X GET "http://localhost:8081/api/v1/empleados/7" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 4. Crear empleado (requiere rol admin/manager)
```bash
curl -X POST "http://localhost:8081/api/v1/empleados" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "ci": "9876543",
    "nombres": "Ana",
    "apellido_paterno": "López",
    "fecha_nacimiento": "1985-03-20",
    "genero": "F",
    "estado_civil": "Casado",
    "celular": "71987654",
    "email": "ana.lopez@ypfb.gov.bo",
    "direccion": "Av. Ejemplo #123",
    "ciudad": "Santa Cruz",
    "codigo_empleado": "YPFB010",
    "fecha_ingreso": "2025-01-15",
    "nacionalidad": "Boliviana"
  }'
```

---

## ⚠️ Códigos de Estado HTTP

| Código | Significado |
|--------|-------------|
| **200** | ✅ Operación exitosa |
| **201** | ✅ Recurso creado exitosamente |
| **401** | ❌ No autenticado (token inválido/faltante) |
| **403** | ❌ Sin permisos (rol insuficiente) |
| **404** | ❌ Recurso no encontrado |
| **422** | ❌ Errores de validación |
| **500** | ❌ Error interno del servidor |

---

## 📋 Formato de Respuestas

### Respuesta exitosa
```json
{
  "success": true,
  "data": { ... },
  "message": "Operación completada"
}
```

### Respuesta con error
```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": {
    "field": ["Mensaje de validación"]
  }
}
```

### Respuesta de lista paginada
```json
{
  "success": true,
  "data": {
    "empleados": [...],
    "resumen": {
      "total_registros": 8,
      "por_estado": {"Activo": 7, "Vacaciones": 1},
      "por_genero": {"M": 4, "F": 4}
    }
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 8,
    "last_page": 1,
    "filtros_aplicados": []
  }
}
```

---

## 🚀 Testing con Postman

1. Importa la colección usando estos endpoints
2. Crea un environment con:
   - `base_url`: `http://localhost:8081/api`
   - `token`: (se llenará después del login)
3. En el endpoint de login, añade este script en "Tests":
   ```javascript
   pm.test("Login successful", function () {
       pm.response.to.have.status(200);
       var jsonData = pm.response.json();
       pm.environment.set("token", jsonData.data.token);
   });
   ```

---

## 📊 Datos de Empleados de Prueba

El sistema incluye 8 empleados de prueba con IDs: **7, 8, 9, 10, 11, 12, 13, 14**

**Ejemplos:**
- ID 7: Juan Carlos González (Admin del sistema)
- ID 8: María Elena Rodríguez (Manager)
- ID 9: Carlos Alberto Mendoza (User)

---

**🔧 Sistema desarrollado con:**
- Laravel 11 + PHP 8.3
- SQL Server 2019
- Laravel Sanctum (autenticación)
- Docker + Docker Compose

**📅 Última actualización:** 2025-09-02