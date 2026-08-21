<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Branch;
use Illuminate\Database\Seeder;

// Crea la clínica demo VETCORESSEN con su sucursal principal en inglés
class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        $clinica = Clinic::create([
            'name'         => 'VETCORESSEN Animal Hospital',
            'ruc'          => '20612345678',
            'razon_social' => 'VETCORESSEN S.A.C.',
            'address'      => '1234 La Molina Ave, Lima',
            'phone'        => '01-555-0100',
            'email'        => 'contact@vetcoressen.com',
            'is_active'    => true,
        ]);

        Branch::create([
            'clinic_id' => $clinica->id,
            'name'      => 'Main Branch',
            'address'   => '1234 La Molina Ave, Lima',
            'phone'     => '01-555-0100',
            'email'     => 'main.branch@vetcoressen.com',
            'principal' => true,
            'is_active' => true,
        ]);
    }
}
