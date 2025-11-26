<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RawStockController;
use App\Http\Controllers\ProductionController;

Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::get('/app', [HomeController::class, 'app'])->name('app');

// Auth routes
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');

// Other routes
Route::get('/raw_stock', [RawStockController::class, 'index'])->name('raw_stock.index');
Route::resource('raw_stock', RawStockController::class);

Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
Route::resource('production', ProductionController::class);