<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\GameScreenshot;

new class extends Component
{
    public $game = [];
    public $screenshots = [];
    public $background = null;

    public function mount($id)
    {
        try {
            $response = Http::get("https://api.rawg.io/api/games/$id", [
                'key' => env('RAWG_API_KEY')
            ]);

            $screens = Http::get("https://api.rawg.io/api/games/$id/screenshots", [
                'key' => env('RAWG_API_KEY')
            ]);

            $this->game = $response->json() ?? [];
            $this->screenshots = $screens->json()['results'] ?? [];

            $this->background = $this->screenshots[0]['image'] ?? $this->game['background_image'] ?? null;

        } catch (\Exception $e) {
            $this->game = [];
            $this->screenshots = [];
        }
    }
};
?>
<div>
    <div
        class="relative h-[400px] bg-cover bg-center"
        style="background-image: url('{{ $background }}')"
    >

        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative max-w-6xl mx-auto h-full flex items-end p-6 text-white md:pl-[420px]">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    {{ $game['name'] ?? 'Jogo' }}
                </h1>
                <p class="text-gray-300">
                    Lançamento: {{ $game['released'] ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex gap-8 flex-col md:flex-row">
            <img
                src="{{ $game['background_image'] ?? 'https://placehold.co/600x400' }}"
                class="relative z-10 w-full md:w-96 rounded shadow -mt-32 border-4 border-white"
            >
            <div>
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

        <div class="mt-10">
            <h2 class="text-xl font-bold mb-3">Descrição</h2>
            <div class="prose max-w-none">
                {!! $game['description'] ?? 'Descrição indisponível.' !!}
            </div>
        </div>
    </div>
</div>