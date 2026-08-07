<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Branch;
use Illuminate\Database\Seeder;

// Crea la clínica demo VETCORESSEN con su sucursal principal
class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        $clinica = Clinic::create([
            'name'       => 'VETCORESSEN S.A.C.',
            'ruc'          => '20612345678',
            'razon_social' => 'VETCORESSEN S.A.C.',
            'address'    => 'Av. La Molina 1234, Lima',
            'phone'     => '01-555-0100',
            'email'        => 'contacto@vetcoressen.pe',
            'is_active'       => true,
        ]);

        Branch::create([
            'clinic_id' => $clinica->id,
            'name'     => 'Sede Principal',
            'address'  => 'Av. La Molina 1234, Lima',
            'phone'   => '01-555-0100',
            'email'      => 'sede.principal@vetcoressen.pe',
            'principal'  => true,
            'is_active'     => true,
        ]);
    }
}
