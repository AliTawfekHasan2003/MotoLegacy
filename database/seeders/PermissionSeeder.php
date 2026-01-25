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
        $permissions  = Permission::all()->pluck('name')->toArray();

        if(!in_array('users.read', $permissions))
            Permission::create(['name' => 'users.read']);
        if(!in_array('users.write', $permissions))
            Permission::create(['name' => 'users.write']);
        if(!in_array('users.delete', $permissions))
            Permission::create(['name' => 'users.delete']);

        if(!in_array('roles.read', $permissions))
            Permission::create(['name' => 'roles.read']);
        if(!in_array('roles.write', $permissions))
            Permission::create(['name' => 'roles.write']);
        if(!in_array('roles.delete', $permissions))
            Permission::create(['name' => 'roles.delete']);

        if(!Role::where('name', 'admin')->exists())
            Role::create([
                'id'         => 1,
                'name'       => 'admin',
                'guard_name' => 'web',
            ]);
        if(!Role::where('name', 'user')->exists())
            Role::create([
                'id'         => 2,
                'name'       => 'user',
                'guard_name' => 'web',
            ]);

        $admin_role = Role::where('name', 'admin')->first();
        $admin_permissions = Permission::pluck('id')->toArray();
        $admin_role->syncPermissions($admin_permissions);
    }
}
