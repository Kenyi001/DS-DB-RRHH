# 🚀 Guía de Onboarding - Equipo Dev Sistema RRHH YPFB

**¡Bienvenido al equipo!** Esta guía te ayudará a configurar tu entorno de desarrollo en menos de 30 minutos.

## 📋 **Pre-requisitos (Instalación única)**

### 1. **Git & GitHub**
```bash
# Verificar instalación
git --version

# Configurar usuario (una sola vez)
git config --global user.name "Tu Nombre"
git config --global user.email "tu.email@company.com"
```

### 2. **Docker Desktop** 
- **Descargar**: https://www.docker.com/products/docker-desktop/
- **Windows**: Docker Desktop for Windows
- **Mac**: Docker Desktop for Mac
- **Linux**: Docker Engine + Docker Compose

**⚠️ IMPORTANTE**: Docker Desktop debe estar ejecutándose antes de trabajar.

### 3. **Editor Recomendado**
- **VS Code** con extensiones:
  - PHP Intelephense
  - Laravel Extension Pack
  - Tailwind CSS IntelliSense
  - Docker (oficial)
  - GitLens

### 4. **Node.js** (para desarrollo frontend)
- **Descargar**: https://nodejs.org/ (versión LTS 20.x)
- **Verificar**: `node --version` y `npm --version`

---

## 🔥 **Setup Rápido (Cada proyecto nuevo)**

### **Paso 1: Clonar el Repositorio**
```bash
# Clonar
git clone https://github.com/Kenyi001/DS-DB-RRHH.git
cd DS-DB-RRHH

# Ver ramas disponibles
git branch -a
git checkout feat/implement-app  # Rama de desarrollo actual
```

### **Paso 2: Verificar Docker**
```bash
# ✅ Verificar que Docker Desktop esté corriendo
docker --version
docker-compose --version

# ✅ Debería mostrar versiones (ej: Docker version 27.x)
```

### **Paso 3: Levantar el Entorno (¡Magia!)**
```bash
# 🪄 Un solo comando levanta TODO el stack
docker-compose up -d

# ✅ Verificar que todo esté corriendo
docker-compose ps
```

**Esperado**: 5 contenedores corriendo:
- ✅ `ds-db-rrhh-app-1` (Laravel PHP 8.3)
- ✅ `ds-db-rrhh-nginx-1` (Servidor web)  
- ✅ `ds-db-rrhh-sqlserver-1` (Base de datos)
- ✅ `ds-db-rrhh-redis-1` (Cache y colas)
- ✅ `ds-db-rrhh-mailhog-1` (Testing email)

### **Paso 4: Configurar Laravel**
```bash
# 🔑 Configurar aplicación Laravel (una sola vez)
docker-compose exec app bash -c "
  cp .env.example .env && 
  php artisan key:generate --force
"

# ✅ Verificar Laravel funcionando
docker-compose exec app php artisan --version
# Debería mostrar: Laravel Framework 11.45.2
```

### **Paso 5: ¡Abrir la Aplicación!**
🌐 **Ve a tu navegador**:
- **Aplicación principal**: http://localhost:8081
- **MailHog (emails)**: http://localhost:8025

**🎉 ¡Listo! Ya tienes el entorno completo funcionando.**

---

## 🛠️ **Comandos Diarios (Cheat Sheet)**

### **Gestión de Contenedores**
```bash
# ▶️ Levantar servicios (inicio del día)
docker-compose up -d

# 📊 Ver estado de contenedores
docker-compose ps

# 📋 Ver logs (debugging)
docker-compose logs app        # Laravel
docker-compose logs sqlserver  # Base de datos
docker-compose logs -f app     # Seguir logs en vivo

# ⏹️ Parar servicios (final del día)
docker-compose down

# 🔄 Reiniciar un servicio específico
docker-compose restart app
```

### **Laravel dentro de Docker**
```bash
# 🎯 Ejecutar comandos Laravel
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller EmpleadoController
docker-compose exec app php artisan tinker

# 📁 Acceder al bash del contenedor
docker-compose exec app bash
```

### **Frontend Development**
```bash
# 📦 Instalar dependencias (primera vez o cuando cambie package.json)
npm ci

# 🔥 Desarrollo con hot reload
npm run dev

# 📦 Build para producción
npm run build

# 🧹 Linting
npm run lint
```

### **Base de Datos**
```bash
# 🔗 Conexión directa a SQL Server
# Host: localhost, Puerto: 1433
# Usuario: sa, Password: YpfbRrhh2024!

# 📊 Desde container
docker-compose exec sqlserver /opt/mssql-tools18/bin/sqlcmd -S localhost -U sa -P YpfbRrhh2024! -C
```

---

## 🔧 **Solución de Problemas Comunes**

### **❌ "No se puede conectar a Docker"**
```bash
# Verificar que Docker Desktop esté corriendo
docker ps

# Si falla: Abrir Docker Desktop desde el menú
# Esperar que diga "Docker Desktop is running"
```

### **❌ "Port already in use"** 
```bash
# Ver qué está usando el puerto
netstat -an | findstr :8081  # Windows
lsof -i :8081                # Mac/Linux

# Cambiar puertos en docker-compose.yml si necesario
```

### **❌ "Laravel no encuentra archivos"**
```bash
# Limpiar cache y reinstalar
docker-compose down
docker-compose up -d --build
docker-compose exec app composer install
```

### **❌ "CSS/JS no aparecen"**
```bash
# Recompilar assets
npm run build
docker-compose restart nginx
```

### **❌ "SQL Server connection failed"**
```bash
# Verificar que SQL Server esté healthy
docker-compose ps

# Ver logs del contenedor
docker-compose logs sqlserver

# Esperar hasta que diga "SQL Server is ready"
```

---

## 📂 **Estructura del Proyecto (Quick Reference)**

```
DS-DB-RRHH/
├── app/Modules/              # 🏗️ Módulos RRHH por dominio
│   ├── Empleados/            # Controllers, Services, Repositories
│   ├── Contratos/            # Models, Requests, Policies, Resources
│   ├── Planilla/             
│   └── ...                   
├── resources/                # 🎨 Frontend
│   ├── views/                # Vistas Blade por módulo
│   ├── css/                  # TailwindCSS + componentes
│   └── js/                   # JavaScript por módulo
├── Docs/                     # 📖 Documentación
│   ├── projectChapter.md     # Documento canónico del proyecto
│   └── sql/draft/            # ⚠️ Scripts SQL (revisar con DBA)
├── docker-compose.yml        # 🐳 Configuración Docker
└── DIRECTORY-STRUCTURE.md    # 📋 Convenciones y estructura
```

---

## 🎨 **Tokens de Diseño YPFB**

### **Colores**
```css
/* Usar estas clases en TailwindCSS */
.bg-ypfb-blue    /* #0A3E8F - Color primario */
.bg-ypfb-red     /* #E31B23 - Accent/danger */
.text-ypfb-blue  /* Texto azul corporativo */

/* Componentes listos */
.btn .btn-primary .btn-danger .btn-secondary
```

### **Rutas & URLs**
- **Web**: `/empleados`, `/contratos`, `/planilla`
- **API**: `/api/v1/empleados`, `/api/v1/contratos`

---

## 🚨 **Reglas del Equipo**

### **Git Workflow**
1. **Siempre** trabajar en ramas feature: `git checkout -b feature/nombre-feature`
2. **Hacer push diario** de tus cambios: `git push origin feature/nombre-feature`
3. **Pull requests** para merge a `feat/implement-app`
4. **Commits descriptivos** con prefijo: `feat:`, `fix:`, `docs:`, etc.

### **Docker**
1. **Siempre** usar `docker-compose` (nunca instalar Laravel local)
2. **Levantar Docker** antes de empezar a trabajar
3. **Apagar Docker** al terminar el día (`docker-compose down`)

### **Código**
1. **Seguir estructura modular**: cada módulo tiene su Controllers/Services/etc.
2. **TailwindCSS only**: usar clases utility, no CSS custom
3. **Testing**: escribir tests para Services y Repositories
4. **SQL Scripts**: NUNCA ejecutar archivos de `/Docs/sql/draft/` sin DBA

### **Comunicación**
1. **Problemas con Docker**: preguntar al equipo primero
2. **Cambios de estructura**: discutir antes de implementar
3. **Base de datos**: coordinar migraciones con el equipo

---

## 📞 **Contactos de Emergencia**

- **Tech Lead**: [Tu nombre aquí]
- **DBA**: [DBA del equipo]
- **Canal Slack/Teams**: #ds-db-rrhh-dev

---

## ✅ **Checklist de Primer Día**

### **Setup Inicial**
- [ ] Docker Desktop instalado y funcionando
- [ ] Repositorio clonado
- [ ] `docker-compose up -d` ejecutado exitosamente  
- [ ] http://localhost:8081 carga correctamente
- [ ] VS Code configurado con extensiones

### **Desarrollo**
- [ ] `npm run dev` funciona
- [ ] Puedo crear un controlador: `docker-compose exec app php artisan make:controller TestController`
- [ ] `npm run build` genera archivos sin errores
- [ ] Git configurado con mi usuario

### **Primer Feature** 
- [ ] Rama feature creada
- [ ] Cambio simple realizado (ej: modificar un texto)
- [ ] Commit y push exitoso
- [ ] Pull request creado

**🎉 ¡Bienvenido al equipo! Ahora estás listo para desarrollar el Sistema RRHH YPFB.**

---

**Última actualización**: 2025-09-01  
**Versión**: 1.0