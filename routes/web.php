<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
    return redirect('/products');
});

// Mahsulotlar (Ombor) routelari
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

// Mijozlar routelari
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::put('/customers/{customer}', [CustomerController::class, 'update']);
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);

// Savdo routelari (POS tizimi)
Route::get('/sales', [App\Http\Controllers\SaleController::class, 'index']);
Route::post('/sales', [App\Http\Controllers\SaleController::class, 'store']);
Route::get('/sales/{sale}', [App\Http\Controllers\SaleController::class, 'show']);
Route::delete('/sales/{sale}', [App\Http\Controllers\SaleController::class, 'destroy']);

// Hisobotlar routelari
Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index']);
