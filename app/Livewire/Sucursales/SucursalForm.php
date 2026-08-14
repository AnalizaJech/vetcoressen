<?php

namespace App\Livewire\Sucursales;

use App\Models\Branch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Formulario de Sucursal')]
class SucursalForm extends Component
{
    public ?Branch $sucursal = null;
    public bool $isEdit = false;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public ?string $ruc = null;

    #[Validate('nullable|string|max:255')]
    public ?string $address = null;

    #[Validate('nullable|string|max:20')]
    public ?string $phone = null;

    #[Validate('nullable|email|max:255')]
    public ?string $email = null;
    
    // Ubicación
    public string $country = '';
    public string $state = '';
    public string $city = '';

    public bool $is_main = false;
    public bool $is_active = true;

    public bool $consultando = false;
    public string $peruApiError = '';

    public function consultarRuc(): void
    {
        $this->peruApiError = '';

        if (empty($this->ruc) || strlen($this->ruc) !== 11) {
            $this->peruApiError = 'El RUC debe tener 11 dígitos.';
            return;
        }

        $this->consultando = true;

        try {
            $apiKey = config('services.peruapi.key');
            if ($apiKey === '[ELIMINADO_POR_SEGURIDAD]') {
                $this->name = 'SUCURSAL PRUEBA';
                $this->address = 'Av. Desarrollo Local 123';
                $this->consultando = false;
                return;
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept'        => 'application/json',
            ])->get(config('services.peruapi.base_url') . '/api/ruc/' . $this->ruc, [
                'api_token' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();
                $this->name = $data['nombre_o_razon_social'] ?? $data['razon_social'] ?? '';
                
                $direccion = $data['direccion_completa'] ?? $data['direccion'] ?? $data['address'] ?? '';
                if ($direccion) {
                    $this->address = $direccion;
                }
                
                if (isset($data['departamento'])) {
                    $this->country = 'PE';
                    $locationService = app(\App\Services\LocationService::class);
                    $states = $locationService->getStates('PE');
                    
                    $depto = strtolower(trim($data['departamento']));
                    foreach ($states as $s) {
                        if (strtolower($s['name']) === $depto || strtolower(str_replace(' ', '', $s['name'])) === str_replace(' ', '', $depto)) {
                            $this->state = $s['iso2'];
                            break;
                        }
                    }
                    
                    if ($this->state && isset($data['distrito'])) {
                        $cities = $locationService->getCities('PE', $this->state);
                        $distrito = strtolower(trim($data['distrito']));
                        foreach ($cities as $c) {
                            if (strtolower($c['name']) === $distrito) {
                                $this->city = $c['name'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $this->peruApiError = $response->json('mensaje') ?? 'No se encontró información para este RUC.';
            }
        } catch (\Exception $e) {
            $this->peruApiError = 'Error al consultar API: ' . $e->getMessage();
        } finally {
            $this->consultando = false;
        }
    }

    public function mount(?int $id = null): void
    {
        $sucursal = $id ? Branch::findOrFail($id) : null;

        if ($sucursal) {
            $this->sucursal = $sucursal;
            $this->isEdit = true;
            $this->name = $sucursal->name;
            $this->ruc = $sucursal->ruc;
            $this->address = $sucursal->address;
            $this->phone = $sucursal->phone;
            $this->email = $sucursal->email;
            $this->country = $sucursal->country ?? '';
            $this->state = $sucursal->state ?? '';
            $this->city = $sucursal->city ?? '';
            $this->is_main = $sucursal->is_main ?? false;
            $this->is_active = $sucursal->is_active ?? true;
        }
    }

    public function guardar()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);
        
        // Si se marca como principal, desactivar las demás
        if ($this->is_main) {
            Branch::query()->update(['is_main' => false]);
            $validated['is_main'] = true;
            // Una sucursal principal siempre debe estar activa
            $this->is_active = true;
        }

        $validated['is_active'] = $this->is_active;

        if ($this->isEdit) {
            $this->sucursal->update($validated);
            session()->flash('mensaje', 'Sucursal actualizada exitosamente.');
        } else {
            // Si es la primera, hacerla principal por defecto
            if (Branch::count() === 0) {
                $validated['is_main'] = true;
            }
            Branch::create($validated);
            session()->flash('mensaje', 'Sucursal creada exitosamente.');
        }

        return $this->redirectRoute('sucursales.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.sucursales.sucursal-form');
    }
}
