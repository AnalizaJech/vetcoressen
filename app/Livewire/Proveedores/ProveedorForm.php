<?php

namespace App\Livewire\Proveedores;

use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Services\LocationService;

#[Layout('components.layouts.app')]
#[Title('Formulario de Proveedor')]
class ProveedorForm extends Component
{
    public ?Supplier $proveedor = null;
    public bool $isEdit = false;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public ?string $ruc = null;

    #[Validate('required|string|max:255')]
    public ?string $contact_name = null;

    #[Validate('required|email|max:255')]
    public ?string $email = null;

    #[Validate('required|string|max:20')]
    public ?string $phone = null;

    #[Validate('nullable|string|max:255')]
    public ?string $address = null;
    
    // Ubicación (País / Estado / Ciudad)
    public ?string $country = null;
    public ?string $state = null;
    public ?string $city = null;

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
                $this->name = 'EMPRESA DE PRUEBA S.A.C.';
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
                
                // Mapeo de dirección considerando posibles formatos de respuesta de APIs de RUC en Perú
                $direccion = $data['direccion_completa'] ?? $data['direccion'] ?? $data['address'] ?? '';
                if ($direccion) {
                    $this->address = $direccion;
                }
                
                // Mapeo de ubicación
                if (isset($data['departamento'])) {
                    $this->country = 'PE';
                    $this->updatedCountry();
                    $locationService = app(LocationService::class);
                    $states = $locationService->getStates('PE');
                    
                    $depto = strtolower(trim($data['departamento']));
                    foreach ($states as $s) {
                        if (strtolower($s['name']) === $depto || strtolower(str_replace(' ', '', $s['name'])) === str_replace(' ', '', $depto)) {
                            $this->state = $s['iso2'];
                            $this->updatedState();
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

    public function updatedCountry(): void
    {
        $this->state = '';
        $this->city = '';
    }

    public function updatedState(): void
    {
        $this->city = '';
    }
    public function mount(?Supplier $proveedor = null)
    {
        if ($proveedor && $proveedor->exists) {
            $this->proveedor = $proveedor;
            $this->isEdit = true;
            $this->name = $proveedor->name;
            $this->ruc = $proveedor->ruc;
            $this->contact_name = $proveedor->contact_name;
            $this->email = $proveedor->email;
            $this->phone = $proveedor->phone;
            $this->address = $proveedor->address;
            $this->country = $proveedor->country ?? '';
            $this->state = $proveedor->state ?? '';
            $this->city = $proveedor->city ?? '';
            $this->is_active = $proveedor->is_active;
        }
    }

    public function guardar()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:20',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);
        
        $validated['is_active'] = $this->is_active;

        if ($this->isEdit) {
            $this->proveedor->update($validated);
            session()->flash('mensaje', 'Proveedor actualizado exitosamente.');
        } else {
            $validated['clinic_id'] = 1;
            Supplier::create($validated);
            session()->flash('mensaje', 'Proveedor creado exitosamente.');
        }

        return $this->redirectRoute('proveedores.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.proveedores.proveedor-form');
    }
}
