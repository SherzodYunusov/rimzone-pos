<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;

// ── Auth routes (guest only) ──────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Password change — no login required, protected by secret keyword only
Route::get('/password', [PasswordController::class, 'showForm'])->name('password.form');
Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

// Logout
Route::middleware('auth')->post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Protected routes ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'nocache'])->group(function () {

    Route::get('/', function () {
        return redirect('/products');
    });

    // Mahsulotlar (Ombor)
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Mijozlar
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);

    // Savdo (POS)
    Route::get('/sales', [App\Http\Controllers\SaleController::class, 'index']);
    Route::post('/sales', [App\Http\Controllers\SaleController::class, 'store']);
    Route::get('/sales/{sale}', [App\Http\Controllers\SaleController::class, 'show']);
    Route::delete('/sales/{sale}', [App\Http\Controllers\SaleController::class, 'destroy']);
    Route::post('/sales/{sale}/pay', [App\Http\Controllers\SaleController::class, 'pay']);

    // Hisobotlar
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index']);
    Route::delete('/reports/clear-day', [App\Http\Controllers\ReportController::class, 'clearDay']);
    Route::delete('/reports/reset-all', [App\Http\Controllers\ReportController::class, 'resetAll']);

    // Chek (receipt)
    Route::get('/receipts/{sale}', [App\Http\Controllers\ReceiptController::class, 'show']);
});
