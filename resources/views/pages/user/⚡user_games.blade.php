<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\GameUser;

new class extends Component
{
    public $games = [];
    public $activeMenu = null;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userGames = GameUser::where('user_id', auth()->id())->get();

        $this->games = $userGames->map(function ($gameUser) {
            return [
                'id' => $gameUser->game_id,
                'title' => $gameUser->title,
                'description' => $gameUser->description,
                'cover' => $gameUser->cover,
                'released_date' => $gameUser->released_date,
                'metacritic_score' => $gameUser->metacritic_score,
                'developers' => $gameUser->developers,
                'publisher' => $gameUser->publisher,
                'status' => $gameUser->status
            ];
        })->toArray();
    }

    public function toggleMenu($gameId)
    {
        if ($this->activeMenu === $gameId) {
            $this->activeMenu = null;
        } else {
            $this->activeMenu = $gameId;
        }
    }
};
?>
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <livewire:header />

    <div class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-bold mb-8">
            Meu Backlog
        </h1>

        @if(count($games) === 0)
            <div class="bg-white p-8 rounded-lg shadow text-center text-gray-500">
                Você ainda não adicionou jogos ao seu backlog.
            </div>

        @else
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach ($games as $game)
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden relative">
                        <div class="">
                            <button wire:click="toggleMenu({{ $game['id'] }})" class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full p-1 shadow">
                                <x-heroicon-o-ellipsis-vertical class="w-5 h-5 text-gray-600" />
                            </button>

                            @if($activeMenu === $game['id'])
                                <div class="absolute right-2 top-10 w-40 bg-white rounded-lg shadow-lg z-10">
                                    <button class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                        Atualizar status
                                    </button>

                                    <button class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-500">
                                        Remover
                                    </button>
                                </div>
                            @endif
                        </div>
                        <img
                            src="{{ $game['cover'] ?? 'https://placehold.co/600x800' }}"
                            class="w-full h-40 object-cover"
                        >

                        <div class="p-3">
                            <h4 class="font-semibold text-sm line-clamp-2">
                                {{ $game['title'] }}
                            </h4>

                            <p class="text-xs text-gray-500">
                                {{ $game['released_date'] ?? 'Sem data' }}
                            </p>

                            <div class="flex items-center gap-1 mt-2">
                                <span class="text-xs bg-green-500 text-white px-2 py-1 rounded flex items-center">
                                    <img src="{{ asset('imgs/metacritic_logo.png') }}" class="w-4 h-4 mr-1">
                                    {{ $game['metacritic_score'] ?? 'N/A' }}
                                </span>

                                <span class="text-xs text-white px-2 py-1 rounded
                                    @if($game['status'] === 'backlog') bg-gray-500
                                    @elseif($game['status'] === 'playing') bg-blue-500
                                    @elseif($game['status'] === 'completed') bg-green-500
                                    @elseif($game['status'] === 'dropped') bg-red-500
                                    @elseif($game['status'] === 'wishlist') bg-purple-500
                                    @endif
                                ">
                                    {{ ucfirst($game['status']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>