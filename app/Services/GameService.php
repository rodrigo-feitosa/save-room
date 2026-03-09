<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GameService
{
    public function search($query)
    {
        $response = Http::get('https://api.rawg.io/api/games', [
            'key' => config('services.rawg.key'),
            'search' => $query
        ]);

        return $response->json()['results'];
    }
}