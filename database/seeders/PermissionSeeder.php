<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'users.manage',
            'cars.read',
            'cars.create',
            'cars.update',
            'cars.delete',
            'requests.create',
            'requests.view_own',
            'requests.manage', // Admin/Seller manage incoming requests
            'messages.read',
            'statistics.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $sellerRole = Role::firstOrCreate(['name' => 'seller']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign permissions to seller
        $sellerRole->syncPermissions([
            'cars.read',
            'cars.create',
            'cars.update',
            'cars.delete',
            'requests.view_own',
            'requests.manage',
        ]);

        // Assign permissions to user
        $userRole->syncPermissions([
            'cars.read',
            'requests.create',
            'requests.view_own',
        ]);
    }
}
