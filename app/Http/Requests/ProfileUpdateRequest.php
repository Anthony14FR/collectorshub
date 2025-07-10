<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'avatar' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $unlocked = $this->user()->unlocked_avatars;
                    if (is_string($unlocked)) {
                        $unlocked = json_decode($unlocked, true);
                    }
                    $unlocked = $unlocked ?? [];
                    if ($value && !in_array($value, $unlocked)) {
                        $fail('Avatar non débloqué.');
                    }
                }
            ],
        ];
    }
}
