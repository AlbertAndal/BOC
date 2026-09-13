<?php

namespace Database\Seeders;

use App\Models\Role as BocRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Seed BOC Application Roles
        $roles = [
            'Administrator',
            'Accounts Manager',
            'Customs Broker',
        ];

        foreach ($roles as $roleName) {
            BocRole::firstOrCreate(['role_name' => $roleName]);
            SpatieRole::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Define Permissions
        $permissions = [
            // Admin permissions
            'manage_users',
            'manage_roles',
            'manage_importers',
            'manage_ports',
            'manage_offices',
            'manage_all_issues',
            'manage_all_actions',

            // Manager permissions
            'view_assigned_issues',
            'update_issue_status',
            'add_issue_actions',

            // Broker permissions
            'create_issues',
            'view_own_issues',

            // Common reference views
            'view_importers',
            'view_ports',
            'view_offices',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 3. Assign Permissions to Spatie Roles
        $adminSpatieRole = SpatieRole::findByName('Administrator', 'web');
        $adminSpatieRole->syncPermissions(Permission::all());

        $managerSpatieRole = SpatieRole::findByName('Accounts Manager', 'web');
        $managerSpatieRole->syncPermissions([
            'view_assigned_issues',
            'update_issue_status',
            'add_issue_actions',
            'view_importers',
            'view_ports',
            'view_offices',
        ]);

        $brokerSpatieRole = SpatieRole::findByName('Customs Broker', 'web');
        $brokerSpatieRole->syncPermissions([
            'create_issues',
            'view_own_issues',
            'view_importers',
            'view_ports',
            'view_offices',
        ]);

        // 4. Create Default Super Administrator User
        $adminBocRole = BocRole::where('role_name', 'Administrator')->first();

        $adminUser = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'System Administrator',
                'email' => 'admin@boc.gov.ph',
                'password' => 'admin123',
                'role_id' => $adminBocRole->role_id,
                'status' => 'Active',
            ]
        );

        $adminUser->syncRoles(['Administrator']);
    }
}
