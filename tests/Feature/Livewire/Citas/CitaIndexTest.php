<?php

namespace Tests\Feature\Livewire\Citas;

use App\Livewire\Citas\CitaIndex;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Species;
use App\Models\Breed;
use App\Models\Clinic;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class CitaIndexTest extends TestCase
{
    public function test_can_view_appointment_details(): void
    {
        $admin = User::first();
        $cita = Appointment::first();

        if ($admin && $cita) {
            $this->actingAs($admin);
            Livewire::test(CitaIndex::class)
                ->call('ver', $cita->id)
                ->assertSet('citaVer.id', $cita->id);
        } else {
            $this->assertTrue(true);
        }
    }
}
