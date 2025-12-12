<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RawStockController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\DetailProductController;
use App\Http\Controllers\QcCheckController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;

// Auth routes (no middleware)
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('/login', [HomeController::class, 'loginSubmit'])->name('login.post');
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

// access welcome page (no middleware)
Route::get('/welcome', [HomeController::class, 'index'])->name('welcome');
Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::get('/app', [HomeController::class, 'app'])->name('app');

// Protected routes (require auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('raw_stock', RawStockController::class);
    Route::resource('production', ProductionController::class);
    Route::resource('qc_check', QcCheckController::class);
    Route::delete('/qc_check/production/{production}', [QcCheckController::class, 'destroyByProduction'])->name('qc_check.destroy_production');
    
    Route::resource('transactions', TransactionController::class);
    
    Route::get('/detail_product', [DetailProductController::class, 'index'])->name('detail_product.index');
    Route::get('/detail_product/production/{production_id}', [DetailProductController::class, 'index'])->name('detail_product.production');
    Route::resource('detail_product', DetailProductController::class)->except(['index']);
});