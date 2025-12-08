<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RawStockController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\DetailProductController;
use App\Http\Controllers\QcCheckController;

Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::get('/app', [HomeController::class, 'app'])->name('app');

// Auth routes
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::post('/login', [HomeController::class, 'loginSubmit'])->name('login.post');
Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

// Public routes (no auth required)
Route::resource('raw_stock', RawStockController::class);
Route::resource('production', ProductionController::class);
Route::resource('qc_check', QcCheckController::class);

// DetailProduct routes
Route::get('/detail_product/{production_id?}', [DetailProductController::class, 'index'])->name('detail_product.index');
Route::resource('detail_product', DetailProductController::class)->except(['index']);