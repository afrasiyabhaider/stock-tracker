<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API routes for authentication
Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])
    ->name('register')
    ->middleware(middleware: 'guest');
// API routes for stock tracking
Route::middleware('auth:sanctum')->prefix('stock')->group(function () {
    Route::get('/quote', action: [StockController::class, 'quote']);
    Route::get('/history', [StockController::class, 'history']);
});
