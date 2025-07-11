<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

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

        $promoWithItems = PromoCode::create([
            'code' => 'POKEBALL5',
            'is_active' => true,
            'expires_at' => now()->addMonths(2),
            'is_global' => true,
            'cash' => 0
        ]);

        $promoWithItems->items()->attach([
            16 => ['quantity' => 5],
            14 => ['quantity' => 2],
            17 => ['quantity' => 1]
        ]);

        $epicPromo = PromoCode::create([
            'code' => 'EPIC777',
            'is_active' => true,
            'expires_at' => now()->addMonth(),
            'is_global' => true,
            'cash' => 777
        ]);

        $epicPromo->items()->attach([
            17 => ['quantity' => 2],
            15 => ['quantity' => 3],
            12 => ['quantity' => 1],
            11 => ['quantity' => 1],
        ]);

        $bulkPromo = PromoCode::create([
            'code' => 'STARTER100',
            'is_active' => true,
            'expires_at' => now()->addMonths(6),
            'is_global' => true,
            'cash' => 100
        ]);

        $bulkPromo->items()->attach([
            16 => ['quantity' => 10],
            13 => ['quantity' => 5],
            1 => ['quantity' => 3],
            2 => ['quantity' => 3],
            3 => ['quantity' => 2],
        ]);
    }
}
