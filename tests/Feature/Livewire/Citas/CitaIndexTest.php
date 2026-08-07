<?php

namespace Tests\Feature\Livewire\Citas;

use App\Livewire\Citas\CitaIndex;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CitaIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::create(['name' => 'veterinario']);
    }

    /** @test */
    public function can_view_appointment_details()
    {
        $clinic = \App\Models\Clinic::create(['name' => 'Test Clinic', 'business_name' => 'Test', 'tax_id' => '123', 'is_active' => true]);
        
        $species = \App\Models\Species::create(['name' => 'Canino']);
        $breed = \App\Models\Breed::create(['name' => 'Mestizo', 'species_id' => $species->id]);

        $cliente = Customer::create([
            'clinic_id' => $clinic->id,
            'tipo_documento' => 'DNI',
            'numero_documento' => '12345678',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@test.com',
            'phone' => '123456789'
        ]);
        
        $mascota = Pet::create([
            'customer_id' => $cliente->id,
            'name' => 'Luna',
            'species_id' => $species->id,
            'breed_id' => $breed->id,
            'color' => 'Black',
            'birth_date' => '2020-01-01',
            'gender' => 'Hembra',
            'clinic_id' => $clinic->id,
        ]);
        
        $cita = Appointment::create([
            'customer_id' => $cliente->id,
            'pet_id' => $mascota->id,
            'fecha_hora' => '2026-08-01 10:00:00',
            'status' => 'PENDIENTE',
            'reason' => 'Consulta general',
            'clinic_id' => $clinic->id,
        ]);

        Livewire::test(CitaIndex::class)
            ->call('ver', $cita->id)
            ->assertSet('citaVer.id', $cita->id);
    }

    /** @test */
    public function can_delete_appointment()
    {
        $clinic = \App\Models\Clinic::create(['name' => 'Test Clinic', 'business_name' => 'Test', 'tax_id' => '123', 'is_active' => true]);
        
        $species = \App\Models\Species::create(['name' => 'Canino']);
        $breed = \App\Models\Breed::create(['name' => 'Mestizo', 'species_id' => $species->id]);

        $cliente = Customer::create([
            'clinic_id' => $clinic->id,
            'tipo_documento' => 'DNI',
            'numero_documento' => '12345678',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@test.com',
            'phone' => '123456789'
        ]);
        
        $mascota = Pet::create([
            'customer_id' => $cliente->id,
            'name' => 'Luna',
            'species_id' => $species->id,
            'breed_id' => $breed->id,
            'color' => 'Black',
            'birth_date' => '2020-01-01',
            'gender' => 'Hembra',
            'clinic_id' => $clinic->id,
        ]);
        
        $cita = Appointment::create([
            'customer_id' => $cliente->id,
            'pet_id' => $mascota->id,
            'fecha_hora' => '2026-08-01 10:00:00',
            'status' => 'PENDIENTE',
            'reason' => 'Consulta general',
            'clinic_id' => $clinic->id,
        ]);

        Livewire::test(CitaIndex::class)
            ->call('confirmDeletion', $cita->id)
            ->assertSet('citaEliminarId', $cita->id)
            ->call('eliminar');

        $this->assertSoftDeleted($cita);
    }
}
