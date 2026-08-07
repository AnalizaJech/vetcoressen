<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Orquestador principal de seeders
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ClinicSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            SpeciesBreedSeeder::class,
        ]);
    }
}
