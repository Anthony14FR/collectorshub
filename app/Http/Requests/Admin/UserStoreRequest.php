<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'level' => ['nullable', 'integer', 'min:1'],
            'experience' => ['nullable', 'integer', 'min:0'],
            'cash' => ['nullable', 'integer', 'min:0'],
            'role' => ['required', Rule::in(User::ROLES)],
            'status' => ['required', Rule::in(User::STATUSES)],
        ];
    }
} 