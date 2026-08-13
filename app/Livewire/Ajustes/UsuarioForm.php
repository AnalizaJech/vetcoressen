<?php

namespace App\Livewire\Ajustes;

use App\Models\User;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Usuario')]
class UsuarioForm extends Component
{
    public ?int $usuarioId = null;
    public string $tipo_documento = 'DNI';
    public string $numero_documento = '';
    public string $name = ''; // Will act as Nombres
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $rol = 'veterinario';
    public string $phone = '';
    public string $address = '';
    public string $country = '';
    public string $state = '';
    public string $city = '';
    public string $notes = '';
    public string $cmvp = '';

    public bool $consultando = false;
    public string $peruApiError = '';

    public function mount(?int $id = null)
    {
        if ($id) {
            $user = User::findOrFail($id);
            $this->usuarioId = $user->id;
            $this->tipo_documento = $user->tipo_documento ?? 'DNI';
            $this->numero_documento = $user->numero_documento ?? $user->dni ?? '';
            $this->name = $user->name;
            $this->last_name = $user->last_name ?? '';
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
            $this->address = $user->address ?? '';
            $this->country = $user->country ?? '';
            $this->state = $user->state ?? '';
            $this->city = $user->city ?? '';
            $this->notes = $user->notes ?? '';
            $this->cmvp = $user->cmvp ?? '';
            // If user has roles, pick the first one
            if ($user->roles->count() > 0) {
                $this->rol = $user->roles->first()->name;
            }
        }
    }

    public function consultarPeruApi(): void
    {
        $this->peruApiError = '';

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
            $endpoint = $this->tipo_documento === 'DNI' ? '/api/dni/' : '/api/ruc/';
            $endpoint .= $this->numero_documento;

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(config('services.peruapi.base_url') . $endpoint, [
                'api_token' => config('services.peruapi.key'),
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();

                if ($this->tipo_documento === 'DNI') {
                    $this->name = $data['nombres'] ?? '';
                    $this->last_name = trim(($data['apellido_paterno'] ?? '') . ' ' . ($data['apellido_materno'] ?? ''));
                } else {
                    $this->name = $data['nombre_o_razon_social'] ?? $data['razon_social'] ?? '';
                    $this->last_name = '';
                    $this->address = $data['address'] ?? $this->address;
                    
                    if (isset($data['departamento'])) {
                        $this->country = 'PE';
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

    public function guardar()
    {
            $this->validate([
            'tipo_documento' => 'required|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:150',
            'email' => 'required|email|max:255|unique:users,email,' . $this->usuarioId,
            'password' => $this->usuarioId ? 'nullable|string|min:8' : 'required|string|min:8',
            'rol' => 'required|exists:roles,name',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'cmvp' => 'nullable|string|max:20',
        ]);

        $datos = [
            'tipo_documento' => $this->tipo_documento,
            'numero_documento' => $this->numero_documento,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'notes' => $this->notes,
            'cmvp' => $this->rol === 'veterinario' ? $this->cmvp : null,
            'dni' => $this->tipo_documento === 'DNI' ? $this->numero_documento : null, // Retrocompatibilidad
        ];

        if ($this->password) {
            $datos['password'] = Hash::make($this->password);
        }

        if ($this->usuarioId) {
            $user = User::findOrFail($this->usuarioId);
            $user->update($datos);
            
            // Sync role
            $user->syncRoles([$this->rol]);

            session()->flash('mensaje', 'Usuario actualizado correctamente.');
        } else {
            $user = User::create($datos);
            $user->assignRole($this->rol);
            session()->flash('mensaje', 'Usuario creado correctamente.');
            
            // Enviar correo de bienvenida asíncrono si tiene email
            if ($user->email) {
                SendWelcomeEmailJob::dispatch($user->email, $user->name . ' ' . $user->last_name, 'Usuario');
            }
        }

        $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render()
    {
        $rolesDisponibles = \Spatie\Permission\Models\Role::orderBy('name')->get();

        return view('livewire.ajustes.usuario-form', [
            'rolesDisponibles' => $rolesDisponibles,
        ]);
    }
}
