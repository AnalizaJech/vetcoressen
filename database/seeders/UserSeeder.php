<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

// Crea el usuario super admin vinculado a la clínica demo
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $clinica = Clinic::first();
        $sucursal = Branch::where('clinic_id', $clinica->id)->where('principal', true)->first();

        $admin = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id' => $sucursal->id,
            'name'        => 'Administrador VETCORESSEN',
            'email'       => 'admin@vetcoressen.pe',
            'password'    => bcrypt('Vetcoressen2026!'), // Password más seguro por defecto
            'phone'    => '999888777',
            'dni'         => '12345678',
            'is_active'      => true,
        ]);

        $admin->assignRole('super_admin');

        // Veterinarios de ejemplo para que el select de veterinario funcione
        $vet1 = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id' => $sucursal->id,
            'name'        => 'Dr. Carlos Rodríguez',
            'email'       => 'carlos@vetcoressen.pe',
            'password'    => bcrypt('Vetcoressen2026!'),
            'phone'    => '999111222',
            'dni'         => '87654321',
            'is_active'      => true,
        ]);
        $vet1->assignRole('veterinario');

        $vet2 = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id' => $sucursal->id,
            'name'        => 'Dra. María López',
            'email'       => 'maria@vetcoressen.pe',
            'password'    => bcrypt('Vetcoressen2026!'),
            'phone'    => '999333444',
            'dni'         => '11223344',
            'is_active'      => true,
        ]);
        $vet2->assignRole('veterinario');

        $vet3 = User::create([
            'clinic_id'  => $clinica->id,
            'branch_id' => $sucursal->id,
            'name'        => 'Dr. Jorge Méndez',
            'email'       => 'jorge@vetcoressen.pe',
            'password'    => bcrypt('Vetcoressen2026!'),
            'phone'    => '999555666',
            'dni'         => '55667788',
            'is_active'      => true,
        ]);
        $vet3->assignRole('veterinario');
    }
}
