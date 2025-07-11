<?php

namespace App\Observers;

use App\Models\Pokedex;
use App\Services\SuccessService;
use App\Services\XpService;

class PokedexObserver
{
    private $successService;
    private $xpService;

    public function __construct(SuccessService $successService, XpService $xpService)
    {
        $this->successService = $successService;
        $this->xpService = $xpService;
    }

    public function created(Pokedex $pokedex)
    {
        $this->successService->checkAndUnlockSuccesses($pokedex->user);
        $this->xpService->addXpForPokedexEntry($pokedex->user, $pokedex);
    }

    public function updated(Pokedex $pokedex)
    {
        $this->successService->checkAndUnlockSuccesses($pokedex->user);
    }
}
