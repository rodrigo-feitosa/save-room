<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\GameUser;

new class extends Component
{
    public $games = [];

    public function mount()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userGames = GameUser::where('user_id', auth()->id())->get();

        foreach ($userGames as $game) {

            $response = Http::get("https://api.rawg.io/api/games/{$game->game_id}", [
                'key' => env('RAWG_API_KEY')
            ]);

            $data = $response->json();

            if ($data) {
                $data['status'] = $game->status;
                $this->games[] = $data;
            }
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
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                        <img
                            src="{{ $game['background_image'] ?? 'https://placehold.co/600x800' }}"
                            class="w-full h-40 object-cover"
                        >
                        <div class="p-3">
                            <h4 class="font-semibold text-sm line-clamp-2">
                                {{ $game['name'] }}
                            </h4>
                            <p class="text-xs text-gray-500">
                                {{ $game['released'] ?? 'Sem data' }}
                            </p>

                            <div class="flex items-center gap-1 mt-2">
                                <span class="text-xs bg-green-500 text-white px-2 py-1 rounded flex items-center">
                                    <img
                                        src="{{ asset('imgs/metacritic_logo.png') }}"
                                        class="w-4 h-4 mr-1"
                                    >
                                    {{ $game['metacritic'] ?? 'N/A' }}
                                </span>
                                <span class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                    {{ $game['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>