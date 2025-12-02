<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RawStockController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\DetailProductController;

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

// DetailProduct routes
// index accepts optional production_id in path: /detail_product or /detail_product/{production_id}
Route::get('/detail_product/{production_id?}', [DetailProductController::class, 'index'])->name('detail_product.index');
// keep resource routes but exclude index to avoid duplicate route
Route::resource('detail_product', DetailProductController::class)->except(['index']);