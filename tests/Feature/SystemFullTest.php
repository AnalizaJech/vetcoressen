<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
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
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function getAdminUser(): User
    {
        return User::where('email', 'admin@vetcoressen.pe')->first();
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
            '/clientes',
            '/clientes/crear',
            '/mascotas',
            '/mascotas/crear',
            '/citas',
            '/citas/crear',
            '/inventario',
            '/inventario/crear',
            '/historias-clinicas',
            '/historias-clinicas/crear',
            '/caja',
            '/caja/venta',
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
}
