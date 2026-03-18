<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\GameUser;

new class extends Component
{
    public $games = [];
    public $status;
    public $activeMenu = null;
    public $selectedGameId;

    public $showModal = false;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userGames = GameUser::where('user_id', auth()->id())->whereNull('deleted_at')->get();

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

    public function openModal($gameId)
    {
        $this->selectedGameId = $gameId;
        $this->showModal = true;
        $this->activeMenu = null;
        $this->status = GameUser::where('game_id', $gameId)
            ->where('user_id', auth()->id())
            ->value('status');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function updateStatus()
    {
        GameUser::where('game_id', $this->selectedGameId)
            ->where('user_id', auth()->id())
            ->update([
                'status' => $this->status
            ]);

        $this->mount();
        $this->closeModal();
    }

    public function removeGame($gameId)
    {
        GameUser::where('game_id', $gameId)
            ->where('user_id', auth()->id())
            ->delete();

        $this->mount();
    }

    
    public function selectGame($gameId)
    {
        return redirect()->route('game_details', ['id' => $gameId]);
    }
};
?>
<div class="min-h-screen bg-gray-100">
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
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden relative cursor-pointer hover:bg-gray-50 hover:scale-[1.02]">
                        <div class="">
                            <button wire:click="toggleMenu({{ $game['id'] }})" class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full p-1 shadow">
                                <x-heroicon-o-ellipsis-vertical class="w-5 h-5 text-gray-600" />
                            </button>

                            @if($activeMenu === $game['id'])
                                <div class="absolute right-2 top-10 w-40 bg-white rounded-lg shadow-lg z-10">
                                    <button wire:click="openModal({{ $game['id'] }})" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                        Atualizar status
                                    </button>

                                    <button wire:click="removeGame({{ $game['id'] }})" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-red-500">
                                        Remover
                                    </button>
                                </div>
                            @endif
                        </div>
                        <img
                            wire:click="selectGame({{ $game['id'] }})"
                            src="{{ $game['cover'] ?? 'https://placehold.co/600x800' }}"
                            class="w-full h-40 object-cover"
                        >

                        <div wire:click="selectGame({{ $game['id'] }})" class="p-3">
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

     @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md ">
                <h2 class="text-xl font-bold mb-4">Atualizar status</h2>

                <form wire:submit.prevent="updateStatus" class="space-y-4">
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