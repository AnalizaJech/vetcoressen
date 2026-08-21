<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

// Crea el usuario super admin y veterinarios de demostración
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $clinica = Clinic::first();
        $sucursal = Branch::where('clinic_id', $clinica->id)->where('principal', true)->first();

        $admin = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id'  => $sucursal->id,
            'name'       => 'VETCORESSEN Administrator',
            'email'      => 'admin@vetcoressen.pe',
            'password'   => bcrypt('Vetcoressen2026!'),
            'phone'      => '999888777',
            'dni'        => '12345678',
            'is_active'  => true,
        ]);

        $admin->assignRole('super_admin');

        // Veterinarios de ejemplo en inglés
        $vet1 = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id'  => $sucursal->id,
            'name'       => 'Dr. Carlos Rodriguez',
            'email'      => 'carlos@vetcoressen.pe',
            'password'   => bcrypt('Vetcoressen2026!'),
            'phone'      => '999111222',
            'dni'        => '87654321',
            'cmvp'       => 'CMVP-4521',
            'is_active'  => true,
        ]);
        $vet1->assignRole('veterinario');

        $vet2 = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id'  => $sucursal->id,
            'name'       => 'Dr. Maria Lopez',
            'email'      => 'maria@vetcoressen.pe',
            'password'   => bcrypt('Vetcoressen2026!'),
            'phone'      => '999333444',
            'dni'        => '11223344',
            'cmvp'       => 'CMVP-3892',
            'is_active'  => true,
        ]);
        $vet2->assignRole('veterinario');

        $vet3 = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id'  => $sucursal->id,
            'name'       => 'Dr. George Mendez',
            'email'      => 'jorge@vetcoressen.pe',
            'password'   => bcrypt('Vetcoressen2026!'),
            'phone'      => '999555666',
            'dni'        => '55667788',
            'cmvp'       => 'CMVP-6103',
            'is_active'  => true,
        ]);
        $vet3->assignRole('veterinario');
    }
}
