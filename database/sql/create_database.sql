-- Crear base de datos sistema_rrhh
-- SCRIPT: DRAFT - Requiere revisión DBA
-- Fecha: 2025-09-02

USE master;
GO

-- Verificar si la base de datos existe
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = N'sistema_rrhh')
BEGIN
    CREATE DATABASE [sistema_rrhh]
    ON (
        NAME = 'sistema_rrhh_data',
        FILENAME = '/var/opt/mssql/data/sistema_rrhh.mdf',
        SIZE = 100MB,
        MAXSIZE = 1GB,
        FILEGROWTH = 10MB
    )
    LOG ON (
        NAME = 'sistema_rrhh_log',
        FILENAME = '/var/opt/mssql/data/sistema_rrhh.ldf',
        SIZE = 10MB,
        MAXSIZE = 100MB,
        FILEGROWTH = 5MB
    );
    
    PRINT 'Base de datos sistema_rrhh creada exitosamente';
END
ELSE
BEGIN
    PRINT 'La base de datos sistema_rrhh ya existe';
END
GO

-- Usar la base de datos
USE [sistema_rrhh];
GO

-- Verificar conexión
SELECT DB_NAME() AS 'Base de datos actual', GETDATE() AS 'Fecha y hora';
GO