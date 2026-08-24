<?php

namespace App\Livewire\Mascotas;

use App\Models\Customer;
use App\Models\Pet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Formulario de registro/edición de mascota
#[Layout('components.layouts.app')]
#[Title('Mascota')]
class MascotaForm extends Component
{
    public ?int $mascotaId = null;
    public string $customer_id = '';
    public string $name = '';
    public ?int $especie_id = null;
    public ?int $raza_id = null;
    public string $gender = 'M';
    public string $color = '';
    public string $birth_date = '';
    public string $peso_actual = '';
    public bool $esterilizado = false;
    public string $medical_notes = '';

    // Pet a visualizar (modal Ver)
    public ?Pet $mascotaVer = null;

    // Búsqueda de cliente
    public string $busquedaCliente = '';

    protected function rules(): array
    {
        return [
            'customer_id'       => 'required|exists:customers,id',
            'name'           => 'required|string|max:100',
            'especie_id'       => 'required|exists:species,id',
            'raza_id'          => 'nullable|exists:breeds,id',
            'gender'             => 'required|in:M,H',
            'color'            => 'nullable|string|max:50',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'peso_actual'      => 'nullable|numeric|min:0|max:500',
            'medical_notes'    => 'nullable|string|max:1000',
        ];
    }

    public function mount(?int $id = null): void
    {
        if ($id) {
            $mascota = Pet::with('cliente')->findOrFail($id);
            $this->mascotaId = $mascota->id;
            $this->customer_id = (string) $mascota->customer_id;
            $this->name = $mascota->name;
            $this->especie_id = $mascota->species_id ? (int) $mascota->species_id : null;
            $this->raza_id = $mascota->raza_id ? (string) $mascota->raza_id : null;
            $this->gender = match ($mascota->gender) { 'Macho' => 'M', 'Hembra' => 'H', default => $mascota->gender ?? 'M' };
            $this->color = $mascota->color ?? '';
            $this->birth_date = $mascota->birth_date?->format('Y-m-d') ?? '';
            $this->peso_actual = $mascota->current_weight ? (string) $mascota->current_weight : '';
            $this->esterilizado = $mascota->esterilizado;
            $this->medical_notes = $mascota->medical_notes ?? '';
            $this->busquedaCliente = $mascota->cliente?->nombre_completo ?? '';
        }
    }

    public function updatedEspecieId(): void
    {
        $this->raza_id = null;
    }

    public function ver(): void
    {
        if ($this->mascotaId) {
            $this->mascotaVer = Pet::with(['cliente', 'especie', 'raza'])->find($this->mascotaId);
        }
    }

    public function eliminar(): void
    {
        if ($this->mascotaId) {
            Pet::findOrFail($this->mascotaId)->delete();
            session()->flash('mensaje', 'Mascota eliminada correctamente.');
            $this->redirect(route('mascotas.index'), navigate: true);
        }
    }

    public function guardar(): void
    {
        $this->validate();

        $sexoNormalizado = in_array($this->gender, ['M', 'H']) ? $this->gender : 'M';

        $datos = [
            'clinic_id'       => 1,
            'customer_id'       => $this->customer_id,
            'name'           => $this->name,
            'species_id'       => $this->especie_id,
            'raza_id'          => $this->raza_id ?: null,
            'gender'             => $sexoNormalizado,
            'color'            => $this->color ?: null,
            'birth_date' => $this->birth_date ?: null,
            'current_weight'      => $this->peso_actual ?: null,
            'esterilizado'     => $this->esterilizado,
            'medical_notes'    => $this->medical_notes ?: null,
        ];

        if ($this->mascotaId) {
            Pet::findOrFail($this->mascotaId)->update($datos);
            session()->flash('mensaje', 'Mascota actualizada correctamente.');
        } else {
            Pet::create($datos);
            session()->flash('mensaje', 'Mascota registrada correctamente.');
        }

        $this->redirect(route('mascotas.index'), navigate: true);
    }

    public function render()
    {
        $clientesDisponibles = Customer::where('is_active', true)
            ->when($this->busquedaCliente, fn ($q) =>
                $q->where('first_name', 'like', "%{$this->busquedaCliente}%")
                  ->orWhere('last_name', 'like', "%{$this->busquedaCliente}%")
                  ->orWhere('numero_documento', 'like', "%{$this->busquedaCliente}%")
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $especies = \App\Models\Species::orderBy('name')->get();
        $razas = $this->especie_id 
            ? \App\Models\Breed::where('species_id', $this->especie_id)->orderBy('name')->get() 
            : [];

        return view('livewire.mascotas.mascota-form', [
            'clientes' => $clientesDisponibles,
            'especies' => $especies,
            'razas' => $razas,
        ]);
    }
}
