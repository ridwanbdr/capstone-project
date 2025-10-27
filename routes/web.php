<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::get('/app', [HomeController::class, 'app'])->name('app');

// Auth routes
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');

// Other routes
Route::get('/icons', [HomeController::class, 'menu1'])->name('menu1');