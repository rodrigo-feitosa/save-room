<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'pages::index')->name('index');
Route::livewire('/register', 'pages::auth.register')->name('register');
Route::livewire('/login', 'pages::auth.login')->name('login');

Route::livewire('/games', 'pages::user.games')->middleware('auth')->name('games');
Route::livewire('/user-games', 'pages::user.user_games')->middleware('auth')->name('user-games');
Route::livewire('/game/{id}', 'pages::game.details')->name('game_details');
