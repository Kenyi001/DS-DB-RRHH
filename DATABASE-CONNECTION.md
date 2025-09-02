# Conexión a Base de Datos SQL Server - YPFB_RRHH

## 🎯 Información de Conexión

**Cadena de conexión principal:**
```
mssql://localhost\SQLEXPRESS/YPFB_RRHH?trusted_connection=true
```

**Detalles de conexión:**
- **Servidor:** `localhost\SQLEXPRESS` (o `DESKTOP-DADKQ5K\SQLEXPRESS`)
- **Puerto dinámico actual:** `64591`
- **Base de datos:** `YPFB_RRHH`
- **Autenticación:** Windows (Integrated Security)
- **Usuario actual:** `DESKTOP-DADKQ5K\Kenji`

## 🔧 Formatos de Conexión

### Para aplicaciones/MCPs que requieren puerto específico:
```
Server=localhost,64591;Database=YPFB_RRHH;Integrated Security=true;Encrypt=false;TrustServerCertificate=true
```

### Para Entity Framework/Laravel:
```
Server=localhost\SQLEXPRESS;Database=YPFB_RRHH;Integrated Security=true;Encrypt=false;TrustServerCertificate=true
```

### Para herramientas de línea de comandos:
```bash
sqlcmd -S ".\SQLEXPRESS" -d "YPFB_RRHH" -E
```

## 🚀 Uso Rápido

### Ejecutar consultas desde PowerShell:
```powershell
# Usar el script incluido
.\sql-query.ps1 "SELECT TOP 5 * FROM INFORMATION_SCHEMA.TABLES"

# O directamente con sqlcmd
sqlcmd -S ".\SQLEXPRESS" -d "YPFB_RRHH" -E -Q "SELECT @@VERSION"
```

### Probar conectividad:
```powershell
Test-NetConnection -ComputerName localhost -Port 64591
```

## 📊 Estructura de Base de Datos

La base de datos `YPFB_RRHH` contiene las tablas definidas en:
- `Docs/Transaccione,Funciones,Procedures.sql`
- Templates en `Docs/sql/`

## ⚠️ Notas Importantes

1. **Puerto dinámico:** El puerto `64591` puede cambiar al reiniciar SQL Server
2. **Solo autenticación Windows:** No se puede usar usuario/contraseña SQL
3. **Firewall:** Puerto TCP 1433 está habilitado en firewall
4. **SQL Browser:** Servicio activo en puerto 1434

## 🔍 Troubleshooting

Si la conexión falla:

1. Verificar puerto actual:
```powershell
netstat -ano | Where-Object { $_ -match 'sqlservr' }
```

2. Verificar servicios:
```powershell
Get-Service -Name 'MSSQL$SQLEXPRESS','SQLBrowser'
```

3. Probar conectividad:
```powershell
Test-NetConnection -ComputerName localhost -Port 64591
```
