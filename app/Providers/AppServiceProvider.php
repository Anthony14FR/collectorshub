<?php

namespace App\Providers;

use App\Models\Pokedex;
use App\Observers\PokedexObserver;
use App\Services\SuccessService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(SuccessService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Pokedex::observe(PokedexObserver::class);
    }
}
