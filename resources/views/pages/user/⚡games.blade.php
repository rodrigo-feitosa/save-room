<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public $games = [];

    public $page = 1;

    public function loadGames()
    {
        $response = Http::get('https://api.rawg.io/api/games', [
            'key' => env('RAWG_API_KEY'),
            'page' => $this->page,
            'page_size' => 24
        ]);

        $newGames = $response->json()['results'] ?? [];

        $this->games = array_merge($this->games, $newGames);

        $this->page++;
    }

    public function mount()
    {
        $this->loadGames();
    }
};
?>
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <livewire:header />

    <div class="mt-5 max-w-9xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach ($games as $game)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition">
                    <img src="{{ $game['background_image'] }}" class="rounded-t-xl object-cover w-full h-40">
                    <div class="p-3">
                        <h4 class="font-semibold text-sm">{{ $game['name'] }}</h4>
                        <p class="text-xs text-gray-500">{{ $game['released'] }}</p>
                        <span class="space-x-2">
                            <span class="text-xs bg-green-500 text-white px-2 py-1 rounded">
                                <img src="{{ asset('imgs/metacritic_logo.png') }}" alt="Metacritic" class="w-4 h-4 inline mr-1">
                                {{ $game['metacritic'] }}
                            </span>
                            <button wire:click="addFromRawg({{ $game['id'] }})" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                Adicionar
                            </button>
                        </span>
                    </div>
                </div>
            @endforeach
            <button wire:click="loadGames" class="mt-6 px-4 py-2 bg-indigo-600 text-white rounded">
                Carregar mais
            </button>
        </div>
    </div>

    <livewire:footer />
</div>