<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpeditionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_admin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'rarity' => ['required', Rule::in(['normal', 'rare', 'epic', 'legendary'])],
            'duration_minutes' => ['required', 'numeric', 'min:0.1'],
            'is_active' => ['boolean'],
            'rewards' => ['required', 'array', 'min:1'],
            'rewards.*.type' => ['required', 'string', Rule::in(['cash', 'xp', 'pokeball', 'masterball', 'item'])],
            'rewards.*.amount' => ['required_unless:rewards.*.type,item', 'integer', 'min:1'],
            'rewards.*.item_id' => ['required_if:rewards.*.type,item', 'integer', 'exists:items,id'],
            'rewards.*.quantity' => ['required_if:rewards.*.type,item', 'integer', 'min:1'],
            'requirements' => ['required', 'array', 'min:1'],
            'requirements.*.type' => ['required', 'string', Rule::in(['rarity', 'type'])],
            'requirements.*.value' => ['required', 'string'],
            'requirements.*.quantity' => ['required', 'integer', 'min:1']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'expédition est requis',
            'description.required' => 'La description est requise',
            'rarity.required' => 'La rareté est requise',
            'rarity.in' => 'La rareté doit être : normal, rare, epic ou legendary',
            'duration_minutes.required' => 'La durée est requise',
            'duration_minutes.min' => 'La durée doit être d\'au moins 0.1 minute',
            'rewards.required' => 'Au moins une récompense est requise',
            'rewards.*.type.required' => 'Le type de récompense est requis',
            'rewards.*.amount.required_unless' => 'La quantité est requise',
            'requirements.required' => 'Au moins un prérequis est requis',
            'requirements.*.type.required' => 'Le type de prérequis est requis',
            'requirements.*.value.required' => 'La valeur du prérequis est requise',
            'requirements.*.quantity.required' => 'La quantité requise est obligatoire'
        ];
    }
}
