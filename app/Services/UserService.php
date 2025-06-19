<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'level' => $data['level'] ?? 1,
            'experience' => $data['experience'] ?? 0,
            'cash' => $data['cash'] ?? 0,
            'role' => $data['role'],
            'status' => $data['status'],
            'last_login' => now(),
        ]);
    }

    public function updateUser(User $user, array $data): bool
    {
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'level' => $data['level'],
            'experience' => $data['experience'],
            'cash' => $data['cash'],
            'role' => $data['role'],
            'status' => $data['status'],
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        return $user->update($userData);
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    public function getUsersWithPagination(int $perPage = 10)
    {
        return User::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getUserWithDetails(User $user): User
    {
        return $user->load(['pokedex', 'inventory']);
    }

    public function canDeleteUser(User $user): bool
    {
        return $user->id !== auth()->id();
    }
} 