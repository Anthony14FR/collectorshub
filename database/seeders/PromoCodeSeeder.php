<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promo1 = PromoCode::firstOrCreate(
            ['code' => 'UNIQUE100'],
            [
                'is_active' => true,
                'expires_at' => now()->addMonths(1),
                'is_global' => false,
                'cash' => 100
            ]
        );

        $promo2 = PromoCode::firstOrCreate(
            ['code' => 'GLOBAL150'],
            [
                'is_active' => true,
                'expires_at' => now()->addMonths(3),
                'is_global' => true,
                'cash' => 150
            ]
        );

        $promoWithItems = PromoCode::firstOrCreate(
            ['code' => 'POKEBALL5'],
            [
                'is_active' => true,
                'expires_at' => now()->addMonths(2),
                'is_global' => true,
                'cash' => 0
            ]
        );

        if ($promoWithItems->wasRecentlyCreated) {
            $promoWithItems->items()->attach([
                16 => ['quantity' => 5],
                14 => ['quantity' => 2],
                17 => ['quantity' => 1]
            ]);
        }

        $epicPromo = PromoCode::firstOrCreate(
            ['code' => 'EPIC777'],
            [
                'is_active' => true,
                'expires_at' => now()->addMonth(),
                'is_global' => true,
                'cash' => 777
            ]
        );

        if ($epicPromo->wasRecentlyCreated) {
            $epicPromo->items()->attach([
                17 => ['quantity' => 2],
                15 => ['quantity' => 3],
                12 => ['quantity' => 1],
                11 => ['quantity' => 1],
            ]);
        }

        $bulkPromo = PromoCode::firstOrCreate(
            ['code' => 'STARTER100'],
            [
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
                'is_global' => true,
                'cash' => 100
            ]
        );

        if ($bulkPromo->wasRecentlyCreated) {
            $bulkPromo->items()->attach([
                16 => ['quantity' => 10],
                13 => ['quantity' => 5],
                1 => ['quantity' => 3],
                2 => ['quantity' => 3],
                3 => ['quantity' => 2],
            ]);
        }
    }
}
