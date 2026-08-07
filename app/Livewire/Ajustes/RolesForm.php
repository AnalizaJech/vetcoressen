<?php

namespace App\Livewire\Ajustes;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Roles y Permisos')]
class RolesForm extends Component
{
    public ?int $roleId = null;
    public string $name = '';
    public array $selectedPermissions = [];

    // Mapeo de permisos a nombres legibles en español
    // Agrupados por módulo para la UI
    protected array $permissionLabels = [
        'Dashboard' => [
            'view_dashboard' => 'Ver dashboard',
        ],
        'Clínicas' => [
            'view_clinics' => 'Ver clínicas',
            'create_clinics' => 'Crear clínicas',
            'edit_clinics' => 'Editar clínicas',
            'delete_clinics' => 'Eliminar clínicas',
        ],
        'Sucursales' => [
            'view_branches' => 'Ver sucursales',
            'create_branches' => 'Crear sucursales',
            'edit_branches' => 'Editar sucursales',
            'delete_branches' => 'Eliminar sucursales',
        ],
        'Usuarios' => [
            'view_users' => 'Ver usuarios',
            'create_users' => 'Crear usuarios',
            'edit_users' => 'Editar usuarios',
            'delete_users' => 'Eliminar usuarios',
        ],
        'Clientes' => [
            'view_clients' => 'Ver clientes',
            'create_clients' => 'Crear clientes',
            'edit_clients' => 'Editar clientes',
            'delete_clients' => 'Eliminar clientes',
        ],
        'Mascotas' => [
            'view_pets' => 'Ver mascotas',
            'create_pets' => 'Crear mascotas',
            'edit_pets' => 'Editar mascotas',
            'delete_pets' => 'Eliminar mascotas',
        ],
        'Citas' => [
            'view_appointments' => 'Ver citas',
            'create_appointments' => 'Crear citas',
            'edit_appointments' => 'Editar citas',
            'cancel_appointments' => 'Cancelar citas',
        ],
        'Historias Clínicas' => [
            'view_records' => 'Ver historias clínicas',
            'create_records' => 'Crear historias clínicas',
            'edit_records' => 'Editar historias clínicas',
        ],
        'Prescripciones' => [
            'view_prescriptions' => 'Ver recetas',
            'create_prescriptions' => 'Crear recetas',
            'dispense_prescriptions' => 'Dispensar recetas',
        ],
        'Inventario' => [
            'view_products' => 'Ver productos',
            'create_products' => 'Crear productos',
            'edit_products' => 'Editar productos',
            'delete_products' => 'Eliminar productos',
        ],
        'Kardex' => [
            'view_kardex' => 'Ver kardex',
            'register_kardex' => 'Registrar movimientos',
        ],
        'Ventas / Caja' => [
            'view_sales' => 'Ver ventas',
            'create_sales' => 'Crear ventas',
            'cancel_sales' => 'Anular ventas',
            'open_register' => 'Abrir caja',
            'close_register' => 'Cerrar caja',
        ],
        'Proveedores' => [
            'view_suppliers' => 'Ver proveedores',
            'create_suppliers' => 'Crear proveedores',
            'edit_suppliers' => 'Editar proveedores',
            'delete_suppliers' => 'Eliminar proveedores',
        ],
        'Reportes' => [
            'view_reports' => 'Ver reportes',
        ],
        'Configuración' => [
            'view_settings' => 'Ver configuración',
            'edit_settings' => 'Editar configuración',
        ],
    ];

    // Iconos Material Symbols por módulo
    protected array $moduleIcons = [
        'Dashboard' => 'dashboard',
        'Clínicas' => 'local_hospital',
        'Sucursales' => 'domain',
        'Usuarios' => 'group',
        'Clientes' => 'person',
        'Mascotas' => 'pets',
        'Citas' => 'calendar_month',
        'Historias Clínicas' => 'clinical_notes',
        'Prescripciones' => 'medication',
        'Inventario' => 'inventory_2',
        'Kardex' => 'receipt_long',
        'Ventas / Caja' => 'point_of_sale',
        'Proveedores' => 'local_shipping',
        'Reportes' => 'assessment',
        'Configuración' => 'settings',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->roleId = $id;
            $role = Role::findOrFail($id);
            $this->name = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|min:3|unique:roles,name,' . $this->roleId,
            'selectedPermissions' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.unique' => 'Este nombre de rol ya está en uso.',
        ];
    }

    public function guardar()
    {
        $this->validate();

        if ($this->roleId && $this->name === 'super_admin') {
            session()->flash('mensaje', 'No puedes modificar el rol Super Admin de esta forma.');
            return redirect()->route('roles.index');
        }

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            $role->update([
                'name' => $this->name,
            ]);
            $role->syncPermissions($this->selectedPermissions);
            $mensaje = 'Rol actualizado correctamente.';
        } else {
            $role = Role::create([
                'name' => $this->name,
                'guard_name' => 'web'
            ]);
            $role->syncPermissions($this->selectedPermissions);
            $mensaje = 'Rol registrado correctamente.';
        }

        session()->flash('mensaje', $mensaje);
        return redirect()->route('roles.index');
    }

    /**
     * Agrupa los permisos existentes en la BD por módulo usando el mapeo.
     * Los permisos que no estén en el mapeo se agrupan en "Otros".
     */
    public function getGroupedPermissions(): array
    {
        $allPermissions = Permission::orderBy('name')->pluck('name')->toArray();
        $grouped = [];
        $mapped = [];

        foreach ($this->permissionLabels as $module => $perms) {
            foreach ($perms as $permName => $label) {
                if (in_array($permName, $allPermissions)) {
                    $grouped[$module][] = [
                        'name' => $permName,
                        'label' => $label,
                    ];
                    $mapped[] = $permName;
                }
            }
        }

        // Permisos no mapeados → grupo "Otros"
        $unmapped = array_diff($allPermissions, $mapped);
        if (!empty($unmapped)) {
            foreach ($unmapped as $permName) {
                $grouped['Otros'][] = [
                    'name' => $permName,
                    'label' => ucfirst(str_replace('_', ' ', $permName)),
                ];
            }
        }

        return $grouped;
    }

    public function render()
    {
        return view('livewire.ajustes.roles-form', [
            'groupedPermissions' => $this->getGroupedPermissions(),
            'moduleIcons' => $this->moduleIcons,
        ]);
    }
}
