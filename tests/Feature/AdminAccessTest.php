<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The public site reads its branding from the settings table,
        // so run the real seeders (roles, admin/accountant users, settings...).
        $this->seed();
    }

    private function userWithRole(?string $role): User
    {
        if ($role === null) {
            return User::factory()->create(['role_id' => null]);
        }

        return User::where('role_id', Role::where('name', $role)->value('id'))->firstOrFail();
    }

    public function test_public_pages_load()
    {
        foreach (['/', '/about', '/contact', '/docters', '/services'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_guest_is_redirected_to_login_from_admin()
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_self_registered_user_without_role_is_forbidden()
    {
        $this->actingAs($this->userWithRole(null))
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_open_dashboard()
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_accountant_can_open_dashboard()
    {
        $this->actingAs($this->userWithRole('accountant'))
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_hods_and_requested_appointments_use_single_admin_prefix()
    {
        $this->assertSame('/admin/hods', route('hods', [], false));
        $this->assertSame('/admin/requestedappointments', route('requestedAppointment', [], false));
    }

    public function test_every_admin_page_renders_for_admin()
    {
        $this->actingAs($this->userWithRole('admin'));

        $adminRoutes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'admin/') && in_array('GET', $route->methods()));

        $this->assertGreaterThanOrEqual(17, $adminRoutes->count());

        foreach ($adminRoutes as $route) {
            $this->get('/'.$route->uri())->assertOk();
        }
    }
}
