<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\Game;

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

    public $showModal = false;

    public $search = '';
    public $searchResults = [];

    public $showMenu = false;

    public function toggleMenu()
    {
        $this->showMenu = !$this->showMenu;
    }

    public function mount()
    {
        $response = Http::get('https://api.rawg.io/api/games', [
            'key' => env('RAWG_API_KEY'),
            'page_size' => 12
        ]);

        $this->games = $response->json()['results'] ?? [];
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

        $response = Http::get('https://api.rawg.io/api/games', [
            'key' => env('RAWG_API_KEY'),
            'search' => $this->search,
            'page_size' => 5
        ]);

        $this->searchResults = $response->json()['results'] ?? [];
    }

    public function selectGame($index)
    {
        $game = $this->searchResults[$index];

        $this->title = $game['name'] ?? '';
        $this->release_date = $game['released'] ?? '';
        $this->cover = $game['background_image'] ?? '';
        $this->metacritic_score = $game['metacritic'] ?? null;

        $this->searchResults = [];
        $this->search = $game['name'];
    }

    public function addGame()
    {
        $game = Game::create([
            'title' => $this->title,
            'description' => $this->description,
            'release_date' => $this->release_date,
            'cover' => $this->cover,
            'developer' => $this->developer,
            'publisher' => $this->publisher,
            'metacritic_score' => $this->metacritic_score,
        ]);

        $this->games[] = $game;

        $this->reset([
            'title',
            'description',
            'release_date',
            'cover',
            'developer',
            'publisher',
            'metacritic_score',
            'search'
        ]);

        $this->searchResults = [];
    }
};
?>

<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">SaveRoom</h1>

            <div class="relative w-72">
                <input
                    type="text"
                    wire:model.debounce.500ms="search"
                    wire:keyup="searchGame"
                    placeholder="Digite o nome do jogo..."
                    class="w-full border rounded px-3 py-2"
                >

                @if(count($searchResults) > 0)
                    <div class="absolute left-0 top-full w-full border rounded mt-1 bg-white max-h-60 overflow-y-auto shadow-lg z-50">
                        @foreach($searchResults as $index => $result)
                            <div
                                wire:click="selectGame({{ $index }})"
                                class="flex items-center p-2 hover:bg-gray-100 cursor-pointer"
                            >

                                <img
                                    src="{{ $result['background_image'] ?? 'https://placehold.co/60x80' }}"
                                    class="w-12 h-16 object-cover rounded mr-3"
                                >

                                <div>
                                    <div class="font-semibold text-sm text-gray-800">
                                        {{ $result['name'] }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $result['released'] }}
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <nav class="space-x-6 relative">
                <a href="#" class="hover:text-gray-300">Home</a>
                <a href="#" class="hover:text-gray-300">Meu Backlog</a>
                <a href="#" class="hover:text-gray-300">Completos</a>
                <a href="#" class="hover:text-gray-300">Wishlist</a>
                <div class="relative inline-block">
                    <button wire:click="toggleMenu" class="hover:text-gray-300">
                        <i class="fa-solid fa-user"></i>
                    </button>

                    @if($showMenu)
                        <div class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg">
                            @auth
                                <a href="/profile" class="block w-full text-left px-4 py-2 text-black hover:bg-gray-100">
                                    Perfil
                                </a>
                                <a href="/backlog" class="block w-full text-left px-4 py-2 text-black hover:bg-gray-100">
                                    Meu Backlog
                                </a>

                                <form method="POST" action="/logout">
                                    @csrf
                                    <button class="block w-full text-left px-4 py-2 text-red-500 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            @endauth

                            @guest
                                <a href="/login" class="block w-full text-left px-4 py-2 text-black hover:bg-gray-100">
                                    Login
                                </a>
                                <a href="/register" class="block w-full text-left px-4 py-2 text-black hover:bg-gray-100">
                                    Registrar
                                </a>
                            @endguest
                        </div>
                    @endif
                </div>
            </nav>
        </div>
    </header>

    <!-- Seção Hero -->
    <section class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-16 text-center">
            <h2 class="text-4xl font-bold mb-4">Organize seu backlog de jogos.</h2>

            <p class="text-gray-600 mb-8">Acompanhe jogos que você quer jogar, está jogando ou já terminou.</p>
            
            <button wire:click="openModal" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Adicionar jogo
            </button>
        </div>
    </section>

    <!--  Lista de Jogos -->
    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold">Lista de Jogos</h3>
            <button class="text-indigo-600 hover:underline">Ver todos</button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach ($games as $game)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition">
                    <img src="{{ $game['background_image'] }}" class="rounded-t-xl w-full">
                    <div class="p-3">
                        <h4 class="font-semibold text-sm">{{ $game['name'] }}</h4>
                        <p class="text-xs text-gray-500">{{ $game['released'] }}</p>
                        @if(isset($game['metacritic']))
                            <span class="text-xs bg-green-500 text-white px-2 py-1 rounded">
                                {{ $game['metacritic'] }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Recent Activity -->
    <section class="bg-white border-t">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <h3 class="text-2xl font-semibold mb-6">Atividade recente</h3>

            <div class="space-y-4">
                <div class="bg-gray-100 p-4 rounded-lg">
                    Você adicionou <strong>Elden Ring</strong> ao backlog
                </div>

                <div class="bg-gray-100 p-4 rounded-lg">
                    Você marcou <strong>Hollow Knight</strong> como concluído
                </div>

                <div class="bg-gray-100 p-4 rounded-lg">
                    Você começou <strong>The Witcher 3</strong>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-10">
        SaveRoom © {{ date('Y') }}
    </footer>


    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Adicionar novo jogo</h2>

                <form wire:submit.prevent="addGame" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Título</label>
                        <input type="text" wire:model="title" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Descrição</label>
                        <textarea wire:model="description" class="w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Data de lançamento</label>
                        <input type="date" wire:model="release_date" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Capa (URL)</label>
                        <input type="text" wire:model="cover" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Desenvolvedor</label>
                        <input type="text" wire:model="developer" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Publicadora</label>
                        <input type="text" wire:model="publisher" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Metacritic Score</label>
                        <input type="number" wire:model="metacritic_score" min="0" max="100" class="w-full border rounded px-3 py-2">
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