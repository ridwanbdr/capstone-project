<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RawStockController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\QcCheckController;

// Auth routes (public)
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('/login', [HomeController::class, 'loginSubmit'])->name('login.post');
Route::get('/register', [HomeController::class, 'register'])->name('register');

// Protected routes (require auth)
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/app', [HomeController::class, 'app'])->name('app');
    Route::post('/logout', [HomeController::class, 'logout'])->name('logout');
    
    Route::get('/raw_stock', [RawStockController::class, 'index'])->name('raw_stock.index');
    Route::resource('raw_stock', RawStockController::class);
    
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::resource('production', ProductionController::class);
    
    Route::resource('qc_check', QcCheckController::class);
});