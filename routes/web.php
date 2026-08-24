<?php

use App\Livewire\Auth\Login;
use App\Livewire\Caja\CajaIndex;
use App\Livewire\Caja\PuntoVenta;
use App\Livewire\Citas\CitaForm;
use App\Livewire\Citas\CitaIndex;
use App\Livewire\Clientes\ClienteForm;
use App\Livewire\Clientes\ClienteIndex;
use App\Livewire\Dashboard;
use App\Livewire\HistoriasClinicas\HistoriaClinicaForm;
use App\Livewire\HistoriasClinicas\HistoriaClinicaIndex;
use App\Livewire\Inventario\ProductoForm;
use App\Livewire\Inventario\ProductoIndex;
use App\Livewire\Mascotas\MascotaForm;
use App\Livewire\Mascotas\MascotaIndex;
use App\Livewire\Proveedores\ProveedorIndex;
use App\Livewire\Proveedores\ProveedorForm;
use App\Livewire\Sucursales\SucursalIndex;
use App\Livewire\Sucursales\SucursalForm;
use App\Livewire\Reportes\ReporteIndex;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas ──
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// ── Rutas autenticadas ──
Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Clientes
    Route::middleware('permission:view_clients')->group(function () {
        Route::get('/clients', ClienteIndex::class)->name('clientes.index');
        Route::get('/clients/create', ClienteForm::class)->name('clientes.crear')->middleware('permission:create_clients');
        Route::get('/clients/{id}/edit', ClienteForm::class)->name('clientes.editar')->middleware('permission:edit_clients');
    });

    // Mascotas
    Route::middleware('permission:view_pets')->group(function () {
        Route::get('/pets', MascotaIndex::class)->name('mascotas.index');
        Route::get('/pets/create', MascotaForm::class)->name('mascotas.crear')->middleware('permission:create_pets');
        Route::get('/pets/{id}/edit', MascotaForm::class)->name('mascotas.editar')->middleware('permission:edit_pets');
        Route::get('/pets/{id}/history/pdf', [\App\Http\Controllers\PdfController::class, 'historialMascota'])->name('mascotas.historial.pdf');
    });

    // Citas
    Route::middleware('permission:view_appointments')->group(function () {
        Route::get('/appointments', CitaIndex::class)->name('citas.index');
        Route::get('/appointments/create', CitaForm::class)->name('citas.crear')->middleware('permission:create_appointments');
        Route::get('/appointments/{id}/edit', CitaForm::class)->name('citas.editar')->middleware('permission:edit_appointments');
        Route::get('/appointments/{id}/pdf', [\App\Http\Controllers\PdfController::class, 'cita'])->name('citas.pdf');
    });

    // Inventario
    Route::middleware('permission:view_products')->group(function () {
        Route::get('/inventory', ProductoIndex::class)->name('inventario.index');
        Route::get('/inventory/create', ProductoForm::class)->name('inventario.crear')->middleware('permission:create_products');
        Route::get('/inventory/entry', \App\Livewire\Inventario\ProductEntryForm::class)->name('inventario.entrada')->middleware('permission:edit_products');
        Route::get('/inventory/{id}/edit', ProductoForm::class)->name('inventario.editar')->middleware('permission:edit_products');
    });

    // Historias Clínicas
    Route::middleware('permission:view_records')->group(function () {
        Route::get('/medical-records', HistoriaClinicaIndex::class)->name('historias.index');
        Route::get('/medical-records/create', HistoriaClinicaForm::class)->name('historias.crear')->middleware('permission:create_records');
        Route::get('/medical-records/{id}/edit', HistoriaClinicaForm::class)->name('historias.editar')->middleware('permission:edit_records');
        Route::get('/medical-records/{id}/view', \App\Livewire\HistoriasClinicas\HistoriaClinicaView::class)->name('historias.ver');
        Route::get('/medical-records/{id}/pdf', [\App\Http\Controllers\PdfController::class, 'historia'])->name('historias.pdf');
    });

    // Caja / Punto de Venta
    Route::middleware('permission:view_sales')->group(function () {
        Route::get('/pos', CajaIndex::class)->name('caja.index');
        Route::get('/pos/sale', PuntoVenta::class)->name('caja.venta')->middleware('permission:create_sales');
        Route::get('/pos/voucher/{id}', \App\Livewire\Caja\Voucher::class)->name('caja.voucher');
        Route::get('/pos/arqueo', \App\Livewire\Caja\CajaArqueo::class)->name('caja.arqueo');
    });

    // Proveedores
    Route::middleware('permission:view_settings')->group(function () { // TODO: change permission if needed
        Route::get('/suppliers', ProveedorIndex::class)->name('proveedores.index');
        Route::get('/suppliers/create', ProveedorForm::class)->name('proveedores.crear');
        Route::get('/suppliers/{id}/edit', ProveedorForm::class)->name('proveedores.editar');
    });

    // Sucursales
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/branches', SucursalIndex::class)->name('sucursales.index');
        Route::get('/branches/create', SucursalForm::class)->name('sucursales.crear');
        Route::get('/branches/{id}/edit', SucursalForm::class)->name('sucursales.editar');
    });

    // Reportes
    Route::middleware('permission:view_reports')->group(function () {
        Route::get('/reports', ReporteIndex::class)->name('reportes.index');
        Route::get('/reports/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'pdf'])->name('reportes.export.pdf');
        Route::get('/reports/export/excel', [\App\Http\Controllers\ReportExportController::class, 'excel'])->name('reportes.export.excel');
    });

    // Administración
    Route::middleware('permission:view_users')->group(function () {
        Route::get('/admin/users', \App\Livewire\Ajustes\UsuariosIndex::class)->name('usuarios.index');
        Route::get('/admin/users/create', \App\Livewire\Ajustes\UsuarioForm::class)->name('usuarios.crear')->middleware('permission:create_users');
        Route::get('/admin/users/{id}/edit', \App\Livewire\Ajustes\UsuarioForm::class)->name('usuarios.editar')->middleware('permission:edit_users');
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/admin/roles', \App\Livewire\Ajustes\RolesIndex::class)->name('roles.index');
        Route::get('/admin/roles/create', \App\Livewire\Ajustes\RolesForm::class)->name('roles.crear');
        Route::get('/admin/roles/{id}/edit', \App\Livewire\Ajustes\RolesForm::class)->name('roles.editar');
    });

    // Configuración
    Route::middleware('permission:view_settings')->group(function () {
        Route::get('/settings/{tab?}', \App\Livewire\Ajustes\AjustesIndex::class)->name('configuracion.index');
    });

    // Logout
    Route::post('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
