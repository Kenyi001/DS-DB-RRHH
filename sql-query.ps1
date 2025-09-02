#!/usr/bin/env pwsh
# Script para ejecutar consultas SQL contra la base de datos YPFB_RRHH
# Uso: .\sql-query.ps1 "SELECT * FROM tabla"

param(
    [Parameter(Mandatory=$true)]
    [string]$Query
)

try {
    Write-Host "Ejecutando consulta SQL..." -ForegroundColor Green
    Write-Host "Query: $Query" -ForegroundColor Cyan
    
    # Ejecutar la consulta usando sqlcmd
    $result = sqlcmd -S ".\SQLEXPRESS" -d "YPFB_RRHH" -E -Q $Query 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Consulta ejecutada exitosamente:" -ForegroundColor Green
        Write-Output $result
    } else {
        Write-Host "❌ Error al ejecutar la consulta:" -ForegroundColor Red
        Write-Output $result
    }
}
catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}
