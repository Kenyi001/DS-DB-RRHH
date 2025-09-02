<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartamentoSeederAprobado::class,
            CargoSeederAprobado::class,
            CategoriaSeeder::class,
            EmpleadoSeederAprobado::class,
            UserSeederMejorado::class,
            ContratoSeeder::class,
        ]);
    }
}