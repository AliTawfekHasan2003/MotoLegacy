<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $sellerRole = Role::where('name', 'seller')->firstOrFail();
        $userRole = Role::where('name', 'user')->firstOrFail();

        $admin = User::create([
            'id'                => 1,
            'name'              => 'admin',
            'email'             => 'admin@gmail.com',
            'password'          => Hash::make('password'),
            'role_id'           => $adminRole->id,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $user_1 = User::create([
            'id'                => 2,
            'name'              => 'ali',
            'email'             => 'ali@gmail.com',
            'password'          => Hash::make('password'),
            'role_id'           => $sellerRole->id,
            'email_verified_at' => now(),
        ]);
        $user_1->assignRole('seller');

        $user_2 = User::create([
            'id'                => 3,
            'name'              => 'sam',
            'email'             => 'sam@gmail.com',
            'password'          => Hash::make('password'),
            'role_id'           => $userRole->id,
            'email_verified_at' => now(),
        ]);
        $user_2->assignRole('user');
    }
}
