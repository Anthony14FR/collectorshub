<?php

namespace App\Observers;

use App\Models\Pokedex;
use App\Services\SuccessService;

class PokedexObserver
{
    private $successService;

    public function __construct(SuccessService $successService)
    {
        $this->successService = $successService;
    }

    public function created(Pokedex $pokedex)
    {
        $this->successService->checkAndUnlockSuccesses($pokedex->user);
    }

    public function updated(Pokedex $pokedex)
    {
        $this->successService->checkAndUnlockSuccesses($pokedex->user);
    }
}
