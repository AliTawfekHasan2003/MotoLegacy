<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        $admin = User::create([
            'id'                => 1,
            'name'         => 'admin',
            'email'             => 'admin@gmail.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $user_1 = User::create([
            'id'                => 2,
            'name'         => 'ali',
            'email'             => 'ali@gmail.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user_1->assignRole('user');

        $user_2 = User::create([
            'id'                => 3,
            'name'              => 'wajdi',
            'email'             => 'wajdi@gmail.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user_2->assignRole('user');
    }
}
