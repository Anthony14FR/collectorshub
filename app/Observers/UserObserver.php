<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $this->giveWelcomeGifts($user);
    }

    private function giveWelcomeGifts(User $user)
    {
        $pokeball = Item::where('name', 'Pokeball')->first();
        $masterball = Item::where('name', 'Masterball')->first();

        if ($pokeball) {
            $inventory = $user->inventory()->firstOrCreate(
                ['item_id' => $pokeball->id],
                ['quantity' => 0]
            );
            $inventory->increment('quantity', 20);
        }

        if ($masterball) {
            $inventory = $user->inventory()->firstOrCreate(
                ['item_id' => $masterball->id],
                ['quantity' => 0]
            );
            $inventory->increment('quantity', 10);
        }

        $user->increment('cash', 50000);
    }
}
