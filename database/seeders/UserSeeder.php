<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@orus.com',
            'password' => Hash::make('admin'),
            'level' => 1,
            'experience' => 12,
            'cash' => 1000,
            'last_login' => now(),
            'role' => 'admin',
            'status' => 'active'
        ]);

        $admin->assignRole('admin');

        $user = User::create([
            'username' => 'user',
            'email' => 'user@orus.com',
            'password' => Hash::make('user'),
            'level' => 1,
            'experience' => 12,
            'cash' => 1000,
            'last_login' => now(),
            'role' => 'user',
            'status' => 'active'
        ]);

        $user->assignRole('user');
    }
}
