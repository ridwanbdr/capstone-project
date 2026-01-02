<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RawStockController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\DetailProductController;
use App\Http\Controllers\QcCheckController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;

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
    // Dashboard - accessible to all authenticated users
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Raw Stock & Production - Admin & Staff Operasional (role check in controller)
    Route::resource('raw_stock', RawStockController::class);
    Route::resource('production', ProductionController::class);
    
    // QC Check - Admin & QC Staff (role check in controller)
    Route::resource('qc_check', QcCheckController::class);
    Route::delete('/qc_check/production/{production}', [QcCheckController::class, 'destroyByProduction'])->name('qc_check.destroy_production');
    
    // Admin only routes (role check in controller)
    Route::resource('transactions', TransactionController::class);
    Route::post('/transactions/mark-paid-by-product/{id}', [TransactionController::class, 'markPendingPaidByProduct'])
        ->name('transactions.markPaidByProduct');
    Route::put('/transactions/bulk-update', [TransactionController::class, 'bulkUpdate'])->name('transactions.bulkUpdate');
    Route::delete('/transactions/bulk-destroy', [TransactionController::class, 'bulkDestroy'])->name('transactions.bulkDestroy');
    Route::put('/transactions/bulk-mark-paid', [TransactionController::class, 'bulkMarkPaid'])->name('transactions.bulkMarkPaid');
    Route::put('/transactions/grouped-update', [TransactionController::class, 'groupedUpdate'])->name('transactions.groupedUpdate');
    Route::get('/detail_product', [DetailProductController::class, 'index'])->name('detail_product.index');
    Route::get('/detail_product/production/{production_id}', [DetailProductController::class, 'index'])->name('detail_product.production');
    Route::delete('/detail_product/production/{productionId}', [DetailProductController::class, 'destroyProduction'])->name('detail_product.destroyProduction');
    Route::resource('detail_product', DetailProductController::class)->except(['index']);
    
    // User management (Admin only)
    Route::resource('users', UserController::class)->middleware('role:admin');

    // User profile (all authenticated users)
    Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('users.updateProfile');

    // Tasks (Admin only for management, assigned users can view their own tasks)
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create')->middleware('role:admin');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store')->middleware('role:admin');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')->middleware('role:admin');
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    
    // Kelola Order (all authenticated users)
    Route::resource('orders', OrderController::class);
    
    // Notifications (all authenticated users)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});