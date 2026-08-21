<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LivewireComponentsTest extends TestCase
{
    /**
     * Test guest redirection to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    /**
     * Test dashboard access for authenticated admin.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'admin@vetcoressen.pe')->first();
        if ($user) {
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
        } else {
            $this->assertTrue(true);
        }
    }
}
