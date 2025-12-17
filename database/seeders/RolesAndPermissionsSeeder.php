<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $modules = ['users', 'products', 'sales', 'purchases', 'suppliers', 'customers', 'reports', 'settings'];
        $actions = ['view', 'create', 'edit', 'delete', 'export'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create(['name' => "{$action} {$module}"]);
            }
        }

        // create roles and assign created permissions

        // Cashier
        $role = Role::create(['name' => 'Cashier']);
        $role->givePermissionTo(['view products', 'view sales', 'create sales', 'view customers', 'create customers']);

        // Manager
        $role = Role::create(['name' => 'Manager']);
         // Give all permissions except settings and users
        $role->givePermissionTo(Permission::all());
        $role->revokePermissionTo(['create users', 'edit users', 'delete users', 'view settings', 'edit settings']);

        // Super-Admin
        $role = Role::create(['name' => 'Super Admin']);
        $role->givePermissionTo(Permission::all());
        
        // Assign Super Admin to the CEO user
        $user = User::where('email', 'ceo@3mcode-solutions.com')->first();
        if ($user) {
            $user->assignRole('Super Admin');
        }
    }
}
