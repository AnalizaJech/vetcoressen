<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Crea los 5 roles del sistema y sus permisos granulares
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos por módulo
        $permisos = [
            // Dashboard
            'view_dashboard',
            // Clínicas y sucursales
            'view_clinics', 'create_clinics', 'edit_clinics', 'delete_clinics',
            'view_branches', 'create_branches', 'edit_branches', 'delete_branches',
            // Usuarios
            'view_users', 'create_users', 'edit_users', 'delete_users',
            // Clientes
            'view_clients', 'create_clients', 'edit_clients', 'delete_clients',
            // Mascotas
            'view_pets', 'create_pets', 'edit_pets', 'delete_pets',
            // Citas
            'view_appointments', 'create_appointments', 'edit_appointments', 'cancel_appointments',
            // Historias clínicas
            'view_records', 'create_records', 'edit_records',
            // Prescripciones
            'view_prescriptions', 'create_prescriptions', 'dispense_prescriptions',
            // Productos / Inventario
            'view_products', 'create_products', 'edit_products', 'delete_products',
            // Kardex
            'view_kardex', 'register_kardex',
            // Ventas / Caja
            'view_sales', 'create_sales', 'cancel_sales',
            'open_register', 'close_register',
            // Reportes
            'view_reports',
            // Configuración
            'view_settings', 'edit_settings',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // ── Roles ──

        // Super Admin - acceso total
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Administrador - todo excepto configuración avanzada
        $admin = Role::firstOrCreate(['name' => 'administrador']);
        $admin->syncPermissions(
            Permission::whereNotIn('name', ['edit_settings'])->get()
        );

        // Veterinario - módulos clínicos + ver inventario
        $vet = Role::firstOrCreate(['name' => 'veterinario']);
        $vet->syncPermissions([
            'view_dashboard',
            'view_clients', 'view_pets', 'edit_pets',
            'view_appointments', 'create_appointments', 'edit_appointments',
            'view_records', 'create_records', 'edit_records',
            'view_prescriptions', 'create_prescriptions', 'dispense_prescriptions',
            'view_products', 'view_kardex',
        ]);

        // Recepcionista - citas, cobros, clientes, mascotas
        $recep = Role::firstOrCreate(['name' => 'recepcionista']);
        $recep->syncPermissions([
            'view_dashboard',
            'view_clients', 'create_clients', 'edit_clients',
            'view_pets', 'create_pets', 'edit_pets',
            'view_appointments', 'create_appointments', 'edit_appointments', 'cancel_appointments',
            'view_sales', 'create_sales',
            'open_register', 'close_register',
        ]);

        // Auxiliar veterinario - lectura clínica, vacunación limitada
        $aux = Role::firstOrCreate(['name' => 'auxiliar_veterinario']);
        $aux->syncPermissions([
            'view_dashboard',
            'view_clients', 'view_pets',
            'view_appointments',
            'view_records',
            'view_prescriptions',
            'view_products',
        ]);
    }
}
