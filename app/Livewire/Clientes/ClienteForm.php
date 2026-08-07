<?php

namespace App\Livewire\Clientes;

use App\Models\Customer;
use App\Services\LocationService;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Formulario de creación/edición de cliente con PeruAPI y ubigeo cascada
#[Layout('components.layouts.app')]
#[Title('Cliente')]
class ClienteForm extends Component
{
    // Datos del cliente
    public ?int $clienteId = null;
    public string $tipo_documento = 'DNI';
    public string $numero_documento = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $notes = '';

    // Ubicación (País / Estado / Ciudad)
    public string $country = '';
    public string $state = '';
    public string $city = '';

    // Estado de la consulta PeruAPI
    public bool $consultando = false;
    public string $peruApiError = '';

    // Customer a visualizar (modal Ver)
    public ?Customer $clienteVer = null;

    // Reglas de validación
    protected function rules(): array
    {
        return [
            'tipo_documento'   => 'required|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento' => 'required|string|max:20',
            'first_name'          => 'required|string|max:150',
            'last_name'        => 'nullable|string|max:150',
            'email'            => 'required|email|max:150',
            'phone'         => 'required|string|max:20',
            'address'        => 'nullable|string|max:255',
            'country'          => 'nullable|string|max:255',
            'state'            => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:500',
        ];
    }

    // Cargar cliente existente para edición
    public function mount(?int $id = null): void
    {
        if ($id) {
            $cliente = Customer::findOrFail($id);
            $this->clienteId = $cliente->id;
            $this->tipo_documento = $cliente->tipo_documento;
            $this->numero_documento = $cliente->numero_documento;
            $this->first_name = $cliente->first_name;
            $this->last_name = $cliente->last_name ?? '';
            $this->email = $cliente->email ?? '';
            $this->phone = $cliente->phone ?? '';
            $this->address = $cliente->address ?? '';
            $this->country = $cliente->country ?? '';
            $this->state = $cliente->state ?? '';
            $this->city = $cliente->city ?? '';
            $this->notes = $cliente->notes ?? '';
        }
    }

    // Consulta asíncrona a PeruAPI (DNI o RUC)
    public function consultarPeruApi(): void
    {
        $this->peruApiError = '';

        // Validar longitud del documento
        if ($this->tipo_documento === 'DNI' && strlen($this->numero_documento) !== 8) {
            $this->peruApiError = 'El DNI debe tener 8 dígitos.';
            return;
        }
        if ($this->tipo_documento === 'RUC' && strlen($this->numero_documento) !== 11) {
            $this->peruApiError = 'El RUC debe tener 11 dígitos.';
            return;
        }

        $this->consultando = true;

        try {
            $apiKey = config('services.peruapi.key');
            if ($apiKey === '[ELIMINADO_POR_SEGURIDAD]') {
                // Mock behavior for local testing
                if ($this->tipo_documento === 'DNI') {
                    $this->first_name = 'USUARIO DE';
                    $this->last_name = 'PRUEBA LOCAL';
                } else {
                    $this->first_name = 'EMPRESA DE PRUEBA S.A.C.';
                    $this->last_name = '';
                    $this->address = 'Av. Desarrollo Local 123';
                }
                $this->consultando = false;
                return;
            }

            $endpoint = $this->tipo_documento === 'DNI' ? '/api/dni/' : '/api/ruc/';
            $endpoint .= $this->numero_documento;

            $response = Http::withHeaders([
                'Accept'        => 'application/json',
            ])->get(config('services.peruapi.base_url') . $endpoint, [
                'api_token' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();

                if ($this->tipo_documento === 'DNI') {
                    $this->first_name = $data['nombres'] ?? '';
                    $this->last_name = trim(($data['apellido_paterno'] ?? '') . ' ' . ($data['apellido_materno'] ?? ''));
                } else {
                    // RUC → razón social va en nombres
                    $this->first_name = $data['nombre_o_razon_social'] ?? $data['razon_social'] ?? '';
                    $this->last_name = '';
                    $this->address = $data['address'] ?? $this->address;
                    
                    // Mapeo de ubicación si es RUC
                    if (isset($data['departamento'])) {
                        $this->country = 'PE';
                        // Forzamos actualización de estados
                        $this->updatedCountry();
                        
                        $locationService = app(\App\Services\LocationService::class);
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
                }
            } else {
                $this->peruApiError = $response->json('mensaje') ?? 'No se encontró información para este documento.';
            }
        } catch (\Exception $e) {
            $this->peruApiError = 'Error al consultar PeruAPI: ' . $e->getMessage();
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

    public function ver(): void
    {
        if ($this->clienteId) {
            $this->clienteVer = Customer::with('mascotas')->find($this->clienteId);
        }
    }

    public function eliminar(): void
    {
        if ($this->clienteId) {
            Customer::findOrFail($this->clienteId)->delete();
            session()->flash('mensaje', 'Cliente eliminado correctamente.');
            $this->redirect(route('clientes.index'), navigate: true);
        }
    }

    // Guardar cliente (crear o actualizar)
    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'clinic_id'        => 1, // Por ahora fijo; se parametriza con multi-clínica
            'tipo_documento'    => $this->tipo_documento,
            'numero_documento'  => $this->numero_documento,
            'first_name'           => $this->first_name,
            'last_name'         => $this->last_name ?: null,
            'email'             => $this->email ?: null,
            'phone'          => $this->phone ?: null,
            'address'         => $this->address ?: null,
            'country'           => $this->country ?: null,
            'state'             => $this->state ?: null,
            'city'              => $this->city ?: null,
            'notes'             => $this->notes ?: null,
            'is_active'            => true,
        ];

        if ($this->clienteId) {
            $cliente = Customer::findOrFail($this->clienteId);
            $cliente->update($datos);
            session()->flash('mensaje', 'Cliente actualizado correctamente.');
        } else {
            $cliente = Customer::create($datos);
            session()->flash('mensaje', 'Cliente registrado correctamente.');
            
            // Enviar correo de bienvenida asíncrono si tiene email
            if ($cliente->email) {
                SendWelcomeEmailJob::dispatch($cliente->email, $cliente->nombre_completo, 'Cliente');
            }
        }

        $this->redirect(route('clientes.index'), navigate: true);
    }

    public function render(LocationService $locationService)
    {
        $countries = $locationService->getCountries();
        
        $states = [];
        if ($this->country) {
            $states = $locationService->getStates($this->country);
        }

        $cities = [];
        if ($this->country && $this->state) {
            $cities = $locationService->getCities($this->country, $this->state);
        }

        return view('livewire.clientes.cliente-form', [
            'countries' => $countries,
            'states'    => $states,
            'cities'    => $cities,
        ]);
    }
}
