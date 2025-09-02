# 👥 README para Equipo Dev - Sistema RRHH YPFB

## 🚀 **Setup Ultra-Rápido (2 comandos)**

```bash
# 1. Clonar y entrar al proyecto
git clone https://github.com/Kenyi001/DS-DB-RRHH.git && cd DS-DB-RRHH

# 2. Setup automático (Windows)
.\dev-setup.ps1

# O setup automático (Mac/Linux)  
./dev-setup.sh

# O usando Make (más simple)
make setup
```

**¡Listo!** Ve a http://localhost:8081

---

## ⚡ **Comandos Diarios (Super Simple)**

### **Con Make (Recomendado)**
```bash
make up          # ▶️ Iniciar día de trabajo
make dev         # 🔥 Desarrollo con hot reload  
make logs        # 📋 Ver qué pasa
make down        # ⏹️ Terminar día
make help        # 📋 Ver todos los comandos
```

### **Docker Directo**
```bash
docker-compose up -d     # ▶️ Levantar servicios
docker-compose down      # ⏹️ Parar servicios  
docker-compose logs -f app  # 📋 Ver logs en vivo
```

### **Desarrollo Frontend** 
```bash
npm run dev      # 🔥 Hot reload (dejar corriendo)
npm run build    # 📦 Build producción
npm run lint     # 🧹 Limpiar código
```

---

## 📂 **¿Dónde trabajo?**

```
app/Modules/              # 🏗️ Tu código principal aquí
├── Empleados/            # Módulo Empleados
│   ├── Controllers/      # 🎯 Controladores web y API
│   ├── Services/         # 💼 Lógica de negocio  
│   ├── Repositories/     # 🗄️ Acceso a datos
│   ├── Models/           # 📊 Modelos Eloquent
│   └── ...
├── Contratos/            # Módulo Contratos
├── Planilla/             # Módulo Planilla
└── ...

resources/                # 🎨 Frontend  
├── views/empleados/      # Vistas por módulo
├── css/                  # TailwindCSS
└── js/                   # JavaScript

tests/                    # ✅ Tests
├── Feature/Modules/      # Tests de endpoints
└── Unit/Modules/         # Tests de servicios
```

---

## 🎨 **Estilos (TailwindCSS)**

### **Colores YPFB**
```html
<!-- Botones principales -->
<button class="btn btn-primary">Guardar</button>
<button class="btn btn-danger">Eliminar</button>

<!-- Colores corporativos -->
<div class="bg-ypfb-blue text-white">Azul YPFB #0A3E8F</div>
<div class="bg-ypfb-red text-white">Rojo YPFB #E31B23</div>
```

### **Componentes Listos**
- `.btn` `.btn-primary` `.btn-danger` `.btn-secondary`
- `.form-input` `.form-select` (con @tailwindcss/forms)

---

## 🌐 **URLs del Proyecto**

| Servicio | URL | Descripción |
|----------|-----|-------------|
| **App Principal** | http://localhost:8081 | Laravel + Nginx |
| **MailHog** | http://localhost:8025 | Testing de emails |
| **SQL Server** | localhost:1433 | sa/YpfbRrhh2024! |
| **Redis** | localhost:6379 | Cache y colas |

---

## 🔄 **Git Workflow**

### **1. Nueva feature**
```bash
git checkout feat/implement-app
git pull origin feat/implement-app
git checkout -b feature/mi-nueva-feature

# Trabajar...
git add .
git commit -m "feat: agregar funcionalidad X"
git push origin feature/mi-nueva-feature
```

### **2. Pull Request**
- IR a GitHub  
- Crear PR desde `feature/mi-nueva-feature` → `feat/implement-app`
- Esperar review del equipo
- Merge cuando esté aprobado

### **3. Mensajes de commit**
```bash
feat: nueva funcionalidad
fix: corrección de bug  
docs: actualizar documentación
style: formato/linting
refactor: refactorización de código
test: agregar tests
```

---

## 🛠️ **Laravel Commands (Dentro de Docker)**

### **Artisan**
```bash
# Crear controller en módulo
docker-compose exec app php artisan make:controller Modules/Empleados/Controllers/EmpleadoController

# Crear migración
docker-compose exec app php artisan make:migration create_empleados_table

# Ejecutar migraciones  
docker-compose exec app php artisan migrate

# Tinker (Laravel REPL)
docker-compose exec app php artisan tinker
```

### **Con Make (más fácil)**
```bash
make artisan CMD="make:controller EmpleadoController"
make db-migrate
make shell  # Bash del contenedor
```

---

## 🆘 **Problemas Comunes**

### **❌ "Docker no responde"**
```bash
# Verificar Docker Desktop esté corriendo
docker ps

# Si falla, reiniciar Docker Desktop
# Windows: Cerrar y abrir Docker Desktop
# Mac: Docker Desktop → Troubleshoot → Restart
```

### **❌ "Puerto ocupado (8081)"**
```bash
# Ver qué usa el puerto
netstat -an | findstr :8081

# O cambiar puerto en docker-compose.yml
ports:
  - "8082:80"  # Cambiar a 8082
```

### **❌ "CSS no aparece"** 
```bash
npm run build
make restart
```

### **❌ "Base de datos no conecta"**
```bash
# Ver logs SQL Server
docker-compose logs sqlserver

# Esperar hasta ver "SQL Server is ready"
# Reintentar después
```

### **❌ "Laravel error 500"**
```bash
# Ver logs Laravel
make logs

# O logs completos
docker-compose logs app
```

### **❌ "Permisos storage"**
```bash
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

---

## ✅ **Testing**

### **Ejecutar tests**
```bash
make test                    # Todos los tests
docker-compose exec app php artisan test --filter EmpleadoTest
```

### **Escribir tests**
- **Feature tests**: `tests/Feature/Modules/Empleados/`
- **Unit tests**: `tests/Unit/Modules/Empleados/`

```php
// Ejemplo test
public function test_puede_crear_empleado()
{
    $response = $this->post('/api/v1/empleados', [
        'nombre' => 'Juan Pérez',
        'ci' => '12345678'
    ]);
    
    $response->assertStatus(201);
}
```

---

## 📊 **Performance & Monitoring**

### **Ver logs en vivo**
```bash
make logs           # App Laravel
docker-compose logs -f redis     # Redis
docker-compose logs -f sqlserver # SQL Server
```

### **Debugging**
- **Laravel**: logs en `storage/logs/laravel.log`
- **Query debugging**: `DB::enableQueryLog()`
- **Tinker**: `make shell` → `php artisan tinker`

---

## 🚨 **Reglas del Equipo**

### **DO ✅**
- Usar `docker-compose` (no instalar Laravel local)
- Seguir estructura modular por dominio
- TailwindCSS only (no CSS custom)
- Tests para Services y Repositories
- Pull request para todo merge
- Push diario de cambios

### **DON'T ❌**
- Ejecutar SQL scripts de `/Docs/sql/draft/` sin DBA
- Cambiar estructura sin discutir
- Commitear archivos `.env` con secretos
- Trabajar directo en `feat/implement-app`
- Instalar dependencias PHP local

---

## 📞 **Ayuda & Soporte**

### **Documentación**
- **TEAM-ONBOARDING.md**: Guía completa de setup
- **DIRECTORY-STRUCTURE.md**: Convenciones y estructura
- **Docs/projectChapter.md**: Documento canónico del proyecto

### **Comandos de ayuda**
```bash
make help           # Ver todos los comandos Make
docker-compose ps   # Estado de contenedores  
make info          # Info del sistema
```

### **Contactos**
- **Tech Lead**: [Nombre]
- **DBA**: [Nombre]  
- **Chat**: #sistema-rrhh-dev

---

**🎉 ¡Happy coding! El sistema RRHH YPFB te está esperando.**

**Última actualización**: 2025-09-01