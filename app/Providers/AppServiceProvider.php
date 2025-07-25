<?php

namespace App\Providers;

use App\Models\Favorite;
use App\Observers\FavoriteObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Favorite::observe(FavoriteObserver::class);
    }
}
