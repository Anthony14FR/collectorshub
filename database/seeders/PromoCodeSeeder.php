<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromoCode;
use App\Models\Item;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        PromoCode::create([
            'code' => 'UNIQUE100',
            'is_active' => true,
            'expires_at' => now()->addMonths(1),
            'is_global' => false,
            'cash' => 100
        ]);

        PromoCode::create([
            'code' => 'GLOBAL150',
            'is_active' => true,
            'expires_at' => now()->addMonths(3),
            'is_global' => true,
            'cash' => 150
        ]);

        $promoWithItem = PromoCode::create([
            'code' => 'POKEBALL5',
            'is_active' => true,
            'expires_at' => now()->addMonths(2),
            'is_global' => true,
            'cash' => 0
        ]);
    }
} 