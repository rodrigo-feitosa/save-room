<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="min-h-screen bg-gray-100">

    <!-- Header -->
    <header class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">GameBacklog</h1>

            <nav class="space-x-6">
                <a href="#" class="hover:text-gray-300">Home</a>
                <a href="#" class="hover:text-gray-300">My Backlog</a>
                <a href="#" class="hover:text-gray-300">Completed</a>
                <a href="#" class="hover:text-gray-300">Wishlist</a>
            </nav>
        </div>
    </header>


    <!-- Hero -->
    <section class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-16 text-center">

            <h2 class="text-4xl font-bold mb-4">
                Organize seu backlog de jogos
            </h2>

            <p class="text-gray-600 mb-8">
                Acompanhe jogos que você quer jogar, está jogando ou já terminou.
            </p>

            <button class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Adicionar jogo
            </button>

        </div>
    </section>


    <!-- Backlog -->
    <section class="max-w-7xl mx-auto px-6 py-12">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold">Seu backlog</h3>

            <button class="text-indigo-600 hover:underline">
                Ver todos
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">

            <!-- Game Card -->
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition">
                <img
                    src="https://placehold.co/300x400"
                    class="rounded-t-xl w-full"
                >

                <div class="p-3">
                    <h4 class="font-semibold text-sm">
                        The Witcher 3
                    </h4>

                    <p class="text-xs text-gray-500">
                        RPG
                    </p>
                </div>
            </div>


            <div class="bg-white rounded-xl shadow hover:shadow-lg transition">
                <img
                    src="https://placehold.co/300x400"
                    class="rounded-t-xl w-full"
                >

                <div class="p-3">
                    <h4 class="font-semibold text-sm">
                        Elden Ring
                    </h4>

                    <p class="text-xs text-gray-500">
                        Soulslike
                    </p>
                </div>
            </div>


            <div class="bg-white rounded-xl shadow hover:shadow-lg transition">
                <img
                    src="https://placehold.co/300x400"
                    class="rounded-t-xl w-full"
                >

                <div class="p-3">
                    <h4 class="font-semibold text-sm">
                        Hollow Knight
                    </h4>

                    <p class="text-xs text-gray-500">
                        Metroidvania
                    </p>
                </div>
            </div>


            <div class="bg-white rounded-xl shadow hover:shadow-lg transition">
                <img
                    src="https://placehold.co/300x400"
                    class="rounded-t-xl w-full"
                >

                <div class="p-3">
                    <h4 class="font-semibold text-sm">
                        Cyberpunk 2077
                    </h4>

                    <p class="text-xs text-gray-500">
                        RPG
                    </p>
                </div>
            </div>

        </div>
    </section>


    <!-- Recent Activity -->
    <section class="bg-white border-t">
        <div class="max-w-7xl mx-auto px-6 py-12">

            <h3 class="text-2xl font-semibold mb-6">
                Atividade recente
            </h3>

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
        GameBacklog © {{ date('Y') }}
    </footer>

</div>