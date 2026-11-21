<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orquesta los seeders del proyecto.
 *
 * No usa WithoutModelEvents porque los modelos dependen de eventos
 * (por ejemplo, Vacante genera su slug al crearse).
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * - CategoriaSeeder y UsuarioPruebaSeeder: datos base obligatorios.
     * - DatosDemoSeeder: información ficticia de demostración.
     */
    public function run(): void
    {
        $this->call([
            CategoriaSeeder::class,
            UsuarioPruebaSeeder::class,
            DatosDemoSeeder::class,
        ]);
    }
}
