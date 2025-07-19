<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();

        $promo1 = PromoCode::firstOrCreate(
            ['code' => 'GLOBAL150'],
            [
                'is_active' => true,
                'expires_at' => now()->addMonths(3),
                'is_global' => true,
                'cash' => 150
            ]
        );

        $pokeball = Item::where('name', 'Pokeball')->first();
        if ($pokeball) {
            $promo2 = PromoCode::firstOrCreate(
                ['code' => 'POKEBALL5'],
                [
                    'is_active' => true,
                    'expires_at' => now()->addMonths(2),
                    'is_global' => true,
                    'cash' => 0
                ]
            );
            if ($promo2->wasRecentlyCreated) {
                $promo2->items()->attach([
                    $pokeball->id => ['quantity' => 5]
                ]);
            }
        }

        $masterball = Item::where('name', 'Masterball')->first();
        if ($masterball) {
            $promo3 = PromoCode::firstOrCreate(
                ['code' => 'MASTERBALL10'],
                [
                    'is_active' => true,
                    'expires_at' => now()->addMonths(2),
                    'is_global' => true,
                    'cash' => 0
                ]
            );
            if ($promo3->wasRecentlyCreated) {
                $promo3->items()->attach([
                    $masterball->id => ['quantity' => 10]
                ]);
            }
        }

        if ($admin) {
            $promo4 = PromoCode::firstOrCreate(
                ['code' => 'ADMIN10'],
                [
                    'is_active' => true,
                    'expires_at' => now()->addMonths(12),
                    'is_global' => false,
                    'cash' => 10000
                ]
            );
            if ($promo4->wasRecentlyCreated) {
                $promo4->users()->attach([
                    $admin->id => ['is_used' => false]
                ]);
            }
        }
    }
}
