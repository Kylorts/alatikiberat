<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// 1. Halaman Utama (Sekarang diarahkan langsung ke Login)
Route::get('/', [AuthController::class, 'showLogin'])->middleware('guest');

// 2. Rute Autentikasi (Hanya bisa diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 3. Rute Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 4. Rute Terproteksi (Hanya bisa diakses SETELAH login)
Route::middleware('auth')->group(function () {
    
    // Rute untuk Admin Gudang
    Route::middleware(['auth', 'role:warehouse_admin'])->group(function () {
        Route::prefix('admin-gudang')->group(function () {
            Route::view('/management', 'admin-gudang-management');
            Route::view('/inbound', 'admin-gudang-inboundstock');
            Route::view('/outbound', 'admin-gudang-outbondstock');
            Route::view('/stock-location', 'admin-gudang-stocklocation');
        });
    });

    // Rute untuk Manajer
    Route::middleware(['auth', 'role:procurement_manager'])->group(function () {
        Route::prefix('manajer-pembelian')->group(function () {
            Route::view('/dashboard', 'manajer-pembelian-dashboard');
            Route::view('/inventory-reports', 'manajer-pembelian-inventoryreports');
            Route::view('/supplier-management', 'manajer-pembelian-suppliermanagement');
        });
    });

});