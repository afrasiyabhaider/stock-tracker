<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('dashboard', function () {
        return Inertia::render(component: 'History');
    })->name('dashboard');
    Route::get('stock', function () {
        return Inertia::render( 'Stock');
    })->name( 'stock');
    Route::get('history', function () {
        return Inertia::render('History');
    })->name('history');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
