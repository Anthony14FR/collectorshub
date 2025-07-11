<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $numberOfUsers = 10;

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $hashedAdminPassword = Hash::make('admin');
        $hashedUserPassword = Hash::make('user');

        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@orus.com',
            'password' => $hashedAdminPassword,
            'level' => 1,
            'experience' => 12,
            'cash' => 1000000,
            'last_login' => now(),
            'role' => 'admin',
            'status' => 'active',
            'avatar' => '/images/trainer/2.png',
            'unlocked_avatars' => json_encode(['/images/trainer/1.png', '/images/trainer/2.png']),
        ]);

        $admin->assignRole($adminRole);

        $user = User::create([
            'username' => 'user',
            'email' => 'user@orus.com',
            'password' => $hashedUserPassword,
            'level' => 1,
            'experience' => 12,
            'cash' => 1000000,
            'last_login' => now(),
            'role' => 'user',
            'status' => 'active',
            'avatar' => '/images/trainer/2.png',
            'unlocked_avatars' => json_encode(['/images/trainer/1.png', '/images/trainer/2.png']),
        ]);

        $user->assignRole($userRole);

        $users = User::factory()
            ->state(['password' => $hashedUserPassword])
            ->count($numberOfUsers)
            ->create();

        foreach ($users as $factoryUser) {
            $factoryUser->assignRole($userRole);
        }
    }
}
