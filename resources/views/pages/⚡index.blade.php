<?php

use App\Models\GameScreenshot;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\GameUser;
use Livewire\Attributes\Layout;
use Illuminate\Http\Client\RequestException;

new #[Layout('layouts::app')] class extends Component
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

    public function toggleMenu()
    {
        $this->showMenu = !$this->showMenu;
    }

    public function mount()
    {
        try{
            $response = Http::get('https://api.rawg.io/api/games', [
                'key' => env('RAWG_API_KEY'),
                'page_size' => 12
            ]);

            $this->games = $response->json()['results'] ?? [];
        } catch (RequestException $e) {
            $this->games = [];
            session()->flash('error', 'Catálogo indisponível');
        }
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

    public function searchGame()
    {
        if(strlen($this->search) < 3){
            $this->searchResults = [];
            return;
        }

        try{
            $response = Http::get('https://api.rawg.io/api/games', [
                'key' => env('RAWG_API_KEY'),
                'search' => $this->search,
                'page_size' => 5
            ]);

            $this->searchResults = $response->json()['results'] ?? [];
        } catch (RequestException $e) {
            $this->searchResults = [];
            session()->flash('error', 'Catálogo indisponível');
        }
    }

    public function selectGame($gameId)
    {
        return redirect()->route('game_details', ['id' => $gameId]);
    }

    public function addGame()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $gameUser = GameUser::create([
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

        $screens = Http::get("https://api.rawg.io/api/games/{$this->rawg_id}/screenshots", [
            'key' => env('RAWG_API_KEY')
        ]);

        foreach(array_slice($screens->json()['results'] ?? [], 0, 5) as $screenshot) {
            GameScreenshot::create([
                'game_id' => $gameUser->id,
                'screenshot' => $screenshot['image'] ?? null,
            ]);
        }

        $this->closeModal();
    }

    public function addFromRawg($rawgId)
    {
        try{
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
        } catch(RequestException $e) {
            session()->flash('error', 'Catálogo indisponível');
        }
    }
};
?>

<div class="min-h-screen">
    <!-- Seção Hero -->
    <section class="border-b">
        <div class="max-w-7xl mx-auto px-6 py-16 text-center">
            <h2 class="text-4xl font-bold mb-4">Organize seu backlog de jogos.</h2>
            <p class="mb-8">Acompanhe jogos que você quer jogar, está jogando ou já terminou.</p>
        </div>
    </section>

    <!--  Lista de Jogos -->
    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold">Lista de Jogos</h3>
            <a href="/games" class="text-indigo-600 hover:underline">Ver todos</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach ($games as $game)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition cursor-pointer hover:bg-gray-50 hover:scale-[1.02]">
                    <img wire:click="selectGame({{ $game['id'] }})" src="{{ $game['background_image'] }}" class="rounded-t-xl object-cover w-full h-40">
                    <div class="p-3">
                        <h4 wire:click="selectGame({{ $game['id'] }})" class="font-semibold text-sm">{{ $game['name'] }}</h4>
                        <p class="text-xs text-gray-500">{{ $game['released'] }}</p>
                        <span class="space-x-2">
                            <span class="text-xs bg-green-500 text-white px-2 py-1 rounded">
                                <img src="{{ asset('imgs/metacritic_logo.png') }}" alt="Metacritic" class="w-4 h-4 inline mr-1">
                                {{ $game['metacritic'] }}
                            </span>
                            <button wire:click="addFromRawg({{ $game['id'] }})" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 cursor-pointer">
                                Adicionar
                            </button>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    
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
</div>