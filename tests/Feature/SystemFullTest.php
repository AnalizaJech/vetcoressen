<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use App\Livewire\Dashboard;
use App\Livewire\Clientes\ClienteIndex;
use App\Livewire\Clientes\ClienteForm;
use App\Livewire\Mascotas\MascotaIndex;
use App\Livewire\Mascotas\MascotaForm;
use App\Livewire\Citas\CitaIndex;
use App\Livewire\Citas\CitaForm;
use App\Livewire\Inventario\ProductoIndex;
use App\Livewire\Inventario\ProductoForm;
use App\Livewire\HistoriasClinicas\HistoriaClinicaIndex;
use App\Livewire\HistoriasClinicas\HistoriaClinicaForm;
use App\Livewire\Caja\CajaIndex;
use App\Livewire\Caja\PuntoVenta;
use Livewire\Livewire;

class SystemFullTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('email', 'admin@vetcoressen.pe')->first() ?? User::first();
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('VETCORESSEN');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_all_authenticated_routes_render_200(): void
    {
        $user = $this->getAdminUser();

        $routes = [
            '/dashboard',
            '/clients',
            '/clients/create',
            '/pets',
            '/pets/create',
            '/appointments',
            '/appointments/create',
            '/inventory',
            '/inventory/create',
            '/medical-records',
            '/medical-records/create',
            '/pos',
            '/pos/sale',
            '/suppliers',
            '/branches',
            '/reports',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200, "Failed to access route: {$route}");
        }
    }

    public function test_livewire_components_mount_cleanly(): void
    {
        $user = $this->getAdminUser();
        $this->actingAs($user);

        Livewire::test(Dashboard::class)->assertStatus(200);
        Livewire::test(ClienteIndex::class)->assertStatus(200);
        Livewire::test(ClienteForm::class)->assertStatus(200);
        Livewire::test(MascotaIndex::class)->assertStatus(200);
        Livewire::test(MascotaForm::class)->assertStatus(200);
        Livewire::test(CitaIndex::class)->assertStatus(200);
        Livewire::test(CitaForm::class)->assertStatus(200);
        Livewire::test(ProductoIndex::class)->assertStatus(200);
        Livewire::test(ProductoForm::class)->assertStatus(200);
        Livewire::test(HistoriaClinicaIndex::class)->assertStatus(200);
        Livewire::test(HistoriaClinicaForm::class)->assertStatus(200);
        Livewire::test(CajaIndex::class)->assertStatus(200);
        Livewire::test(PuntoVenta::class)->assertStatus(200);
    }

    public function test_reports_pdf_and_excel_export(): void
    {
        $user = $this->getAdminUser();
        $this->actingAs($user);

        $pdfResponse = $this->get('/reports/export/pdf?periodo=mes_actual');
        $pdfResponse->assertStatus(200);
        $this->assertTrue(str_contains($pdfResponse->headers->get('content-type'), 'application/pdf'));

        $excelResponse = $this->get('/reports/export/excel?periodo=mes_actual');
        $excelResponse->assertStatus(200);
        $this->assertTrue(str_contains($excelResponse->headers->get('content-type'), 'text/csv'));
    }
}
