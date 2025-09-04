<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Exception;

class CreateDatabaseCommand extends Command
{
    protected $signature = 'db:create {database?} {--connection=}';
    protected $description = 'Create a new database';

    public function handle()
    {
        $databaseName = $this->argument('database') ?? config('database.connections.sqlsrv.database');
        $connection = $this->option('connection') ?? config('database.default');

        $this->info("Intentando crear la base de datos: {$databaseName}");

        try {
            // Configurar conexión temporal a master
            Config::set("database.connections.{$connection}.database", 'master');
            DB::purge($connection);

            // Verificar si la base de datos existe
            $databases = DB::connection($connection)
                ->select("SELECT name FROM sys.databases WHERE name = ?", [$databaseName]);

            if (count($databases) > 0) {
                $this->info("La base de datos '{$databaseName}' ya existe.");
                return 0;
            }

            // Crear la base de datos
            $query = "CREATE DATABASE [{$databaseName}]";
            DB::connection($connection)->statement($query);

            $this->info("¡Base de datos '{$databaseName}' creada exitosamente!");

            // Restaurar configuración original
            Config::set("database.connections.{$connection}.database", $databaseName);
            DB::purge($connection);

            return 0;

        } catch (Exception $e) {
            $this->error("Error al crear la base de datos: " . $e->getMessage());
            return 1;
        }
    }
}