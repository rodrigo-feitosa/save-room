<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'pages::index');
Route::livewire('/register', 'pages::auth.register');
Route::livewire('/login', 'pages::auth.login');

Route::livewire('/games', 'pages::user.games')->middleware('auth');
Route::livewire('/user-games', 'pages::user.user_games')->middleware('auth');