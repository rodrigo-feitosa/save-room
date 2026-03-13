<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public $game = [];

    public function mount($id)
    {
        $response = Http::get("https://api.rawg.io/api/games/$id", [
            'key' => env('RAWG_API_KEY')
        ]);

        $this->game = $response->json();
    }
};
?>

<div class="max-w-6xl mx-auto p-6">
    <div class="flex gap-8">
        <img
            src="{{ $game['background_image'] ?? 'https://placehold.co/600x400' }}"
            class="w-96 rounded shadow"
        >

        <div>
            <h1 class="text-3xl font-bold mb-4">
                {{ $game['name'] }}
            </h1>
            <p class="text-gray-600 mb-2">
                Data de lançamento: {{ $game['released'] ?? 'N/A' }}
            </p>
            <p class="text-gray-600 mb-2">
                Metacritic: {{ $game['metacritic'] ?? 'N/A' }}
            </p>

            <div class="mb-4">
                <h2 class="font-semibold mb-2">Plataformas</h2>
                @foreach($game['platforms'] ?? [] as $platform)
                    <span class="bg-gray-200 px-2 py-1 rounded text-sm mr-2">
                        {{ $platform['platform']['name'] }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-3">Descrição</h2>
        <div class="prose max-w-none">
            {!! $game['description'] !!}
        </div>
    </div>
</div>