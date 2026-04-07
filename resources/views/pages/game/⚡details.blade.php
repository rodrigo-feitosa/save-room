<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\GameScreenshot;

new class extends Component
{
    public $game = [];
    public $screenshots = [];
    public $background = null;
    public $showModal = false;

    public $title;
    public $description;
    public $release_date;
    public $cover;
    public $developer;
    public $publisher;
    public $metacritic_score;
    public $status = 'backlog';
    public $rawg_id;

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

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

    public function addFromRawg($rawgId)
    {
        $this->rawg_id = $rawgId;

        $response = Http::get("https://api.rawg.io/api/games/{$rawgId}", [
            'key' => env('RAWG_API_KEY')
        ]);

        $game = $response->json();

        $this->title = $game['name'] ?? '';
        $this->description = strip_tags($game['description'] ?? '');
        $this->release_date = $game['released'] ?? '';
        $this->cover = $game['background_image'] ?? '';
        $this->metacritic_score = $game['metacritic'] ?? null;

        $this->developer = $game['developers'][0]['name'] ?? '';
        $this->publisher = $game['publishers'][0]['name'] ?? '';

        $this->showModal = true;
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
                class="relative w-full md:w-96 rounded shadow -mt-32 border-4 border-white"
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

                <button 
                    wire:click="addFromRawg({{ $game['id'] }})" 
                    class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 cursor-pointer">
                    Adicionar
                </button>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-xl font-bold mb-3">Descrição</h2>
            <div class="prose max-w-none">
                {!! $game['description'] ?? 'Descrição indisponível.' !!}
            </div>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold mb-4">Adicionar novo jogo</h2>

                <form wire:submit.prevent="addGame" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Título</label>
                        <input type="text" wire:model="title" class="w-full border rounded px-2 py-2" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Descrição</label>
                        <textarea wire:model="description" class="w-full border rounded px-2 py-2" readonly></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Data de lançamento</label>
                        <input type="date" wire:model="release_date" class="w-full border rounded px-2 py-2" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Capa (URL)</label>
                        <input type="text" wire:model="cover" class="w-full border rounded px-2 py-2" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Desenvolvedor</label>
                        <input type="text" wire:model="developer" class="w-full border rounded px-2 py-2" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Publicadora</label>
                        <input type="text" wire:model="publisher" class="w-full border rounded px-2 py-2" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Metacritic Score</label>
                        <input type="number" wire:model="metacritic_score" min="0" max="100" class="w-full border rounded px-2 py-2" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select wire:model="status" class="w-full border rounded px-2 py-2">
                            <option value="backlog">Backlog</option>
                            <option value="playing">Jogando</option>
                            <option value="completed">Completado</option>
                            <option value="dropped">Abandonado</option>
                            <option value="wishlist">Wishlist</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 cursor-pointer">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>