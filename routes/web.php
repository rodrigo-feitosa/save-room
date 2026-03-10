<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'pages::index');
Route::livewire('/register', 'pages::auth.register');
Route::livewire('/login', 'pages::auth.login');