<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public $search = '';
    public $searchResults = [];
    public $showMenu = false;

    public function toggleMenu()
    {
        $this->showMenu = !$this->showMenu;
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

    public function logout()
    {
        auth()->logout();
        return redirect('/login');
    }
};
?>

<header class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <a href="/">
            <h1 class="text-2xl font-bold">SaveRoom</h1>
        </a>

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
            <a href="/" class="hover:text-gray-300">Home</a>
            <a href="/user-games" class="hover:text-gray-300">Meus jogos</a>
            <div class="relative inline-block">
                <button wire:click="toggleMenu" class="hover:text-gray-300 cursor-pointer">
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
    
                            <button wire:click="logout" class="block w-full text-left px-4 py-2 text-red-500 hover:bg-gray-100">
                                Logout
                            </button>
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