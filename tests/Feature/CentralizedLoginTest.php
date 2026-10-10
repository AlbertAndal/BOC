<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Tests\TestCase;

class CentralizedLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    protected function getOrCreateUserWithRole(string $roleName, string $username): User
    {
        $role = Role::firstOrCreate(['role_name' => $roleName]);

        $user = User::firstOrCreate(
            ['username' => $username],
            [
                'full_name' => "Test {$roleName}",
                'email' => "{$username}@example.com",
                'password' => 'password',
                'role_id' => $role->role_id,
                'status' => 'Active',
            ]
        );

        $user->syncRoles([$roleName]);

        return $user;
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Bureau of Customs');
    }

    public function test_universal_login_page_renders_without_portal_switcher(): void
    {
        $response = $this->get('/login');

        $response->assertSuccessful();
        $response->assertDontSee('Choose your portal');
    }

    public function test_legacy_panel_login_routes_redirect_to_universal_login(): void
    {
        $this->get('/admin/login')->assertRedirect('/login');
        $this->get('/manager/login')->assertRedirect('/login');
        $this->get('/broker/login')->assertRedirect('/login');
    }

    public function test_unauthenticated_panel_visits_redirect_to_universal_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/manager')->assertRedirect('/login');
        $this->get('/broker')->assertRedirect('/login');
    }

    public function test_can_access_panel_allows_all_active_roles_on_auth_panel(): void
    {
        $admin = $this->getOrCreateUserWithRole('Administrator', 'test_admin_auth');
        $manager = $this->getOrCreateUserWithRole('Accounts Manager', 'test_mgr_auth');
        $broker = $this->getOrCreateUserWithRole('Customs Broker', 'test_broker_auth');

        $authPanel = Filament::getPanel('auth');

        $this->assertTrue($admin->canAccessPanel($authPanel));
        $this->assertTrue($manager->canAccessPanel($authPanel));
        $this->assertTrue($broker->canAccessPanel($authPanel));
    }

    public function test_administrator_login_response_redirects_to_admin(): void
    {
        $admin = $this->getOrCreateUserWithRole('Administrator', 'test_admin_resp');
        $this->actingAs($admin);

        /** @var LoginResponseContract $response */
        $response = app(LoginResponseContract::class);
        $redirect = $response->toResponse(Request::create('/login', 'POST'));

        $this->assertEquals(url('/admin'), $redirect->getTargetUrl());
    }

    public function test_manager_login_response_redirects_to_manager(): void
    {
        $manager = $this->getOrCreateUserWithRole('Accounts Manager', 'test_mgr_resp');
        $this->actingAs($manager);

        /** @var LoginResponseContract $response */
        $response = app(LoginResponseContract::class);
        $redirect = $response->toResponse(Request::create('/login', 'POST'));

        $this->assertEquals(url('/manager'), $redirect->getTargetUrl());
    }

    public function test_broker_login_response_redirects_to_broker(): void
    {
        $broker = $this->getOrCreateUserWithRole('Customs Broker', 'test_broker_resp');
        $this->actingAs($broker);

        /** @var LoginResponseContract $response */
        $response = app(LoginResponseContract::class);
        $redirect = $response->toResponse(Request::create('/login', 'POST'));

        $this->assertEquals(url('/broker'), $redirect->getTargetUrl());
    }

    public function test_panel_access_is_strictly_enforced_per_role(): void
    {
        $manager = $this->getOrCreateUserWithRole('Accounts Manager', 'test_mgr_access');
        $this->actingAs($manager);
        $this->get('/admin')->assertForbidden();

        $broker = $this->getOrCreateUserWithRole('Customs Broker', 'test_broker_access');
        $this->actingAs($broker);
        $this->get('/admin')->assertForbidden();

        $admin = $this->getOrCreateUserWithRole('Administrator', 'test_admin_access');
        $this->actingAs($admin);
        $this->get('/manager')->assertForbidden();
        $this->get('/broker')->assertForbidden();
    }

    public function test_livewire_login_form_authenticates_administrator_and_redirects(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('auth'));
        $admin = $this->getOrCreateUserWithRole('Administrator', 'admin_livewire_test');

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $admin->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_livewire_login_form_authenticates_manager_and_redirects(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('auth'));
        $manager = $this->getOrCreateUserWithRole('Accounts Manager', 'manager_livewire_test');

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $manager->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect('/manager');

        $this->assertAuthenticatedAs($manager);
    }

    public function test_livewire_login_form_authenticates_broker_and_redirects(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('auth'));
        $broker = $this->getOrCreateUserWithRole('Customs Broker', 'broker_livewire_test');

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $broker->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect('/broker');

        $this->assertAuthenticatedAs($broker);
    }

    public function test_inactive_user_cannot_authenticate_and_sees_approval_message(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('auth'));
        $role = Role::firstOrCreate(['role_name' => 'Customs Broker']);
        $inactive = User::firstOrCreate(
            ['username' => 'inactive_user_test'],
            [
                'full_name' => 'Inactive User',
                'email' => 'inactive@example.com',
                'password' => 'password',
                'role_id' => $role->role_id,
                'status' => 'Inactive',
            ]
        );

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $inactive->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest();
    }
}
