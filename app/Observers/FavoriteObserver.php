<?php

namespace App\Observers;

use App\Models\Favorite;
use Illuminate\Support\Facades\Http;

class FavoriteObserver
{
    /**
     * Handle the Favorite "created" event.
     */
    public function created(Favorite $favorite): void
    {
        //
    }

    /**
     * Handle the Favorite "updated" event.
     */
    public function updated(Favorite $favorite): void
    {
        //
    }

    /**
     * Handle the Favorite "deleted" event.
     */
    public function deleted(Favorite $favorite): void
    {
        //
    }

    /**
     * Handle the Favorite "restored" event.
     */
    public function restored(Favorite $favorite): void
    {
        //
    }

    /**
     * Handle the Favorite "force deleted" event.
     */
    public function forceDeleted(Favorite $favorite): void
    {
        //
    }

    public function creating(Favorite $favorite)
    {
        $response = Http::get('https://fakestoreapi.com/products/' . $favorite->product_id);
        if (!$response->ok()) {
            throw new \Exception('Produto não encontrado na API externa.');
        }
    }
}
