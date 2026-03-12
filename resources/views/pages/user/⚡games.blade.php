<?php

use App\Models\Game;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\GameUser;

new class extends Component
{
    public $games = [];

    public $title;
    public $description;
    public $release_date;
    public $cover;
    public $developer;
    public $publisher;
    public $metacritic_score;
    public $status = 'backlog';

    public $showModal = false;

    public $search = '';
    public $searchResults = [];

    public $showMenu = false;

    public $rawg_id;

    public $page = 1;

    public function loadGames()
    {
        $userGames = GameUser::where('user_id', auth()->id())->get();

        $this->games = array_merge($this->games, $userGames->toArray());

        $this->page++;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->searchResults = [];
        $this->search = '';
    }

    public function mount()
    {
        $this->loadGames();
    }

    public function addGame()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        GameUser::create([
            'user_id' => auth()->id(),
            'game_id' => $this->rawg_id,
            'status' => $this->status,
            'title' => $this->title,
            'description' => $this->description,
            'cover' => $this->cover,
            'released_date' => $this->release_date,
            'metacritic_score' => $this->metacritic_score,
            'developers' => $this->developer,
            'publisher' => $this->publisher,
        ]);

        $this->closeModal();
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

    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md ">
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
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <livewire:footer />
</div>