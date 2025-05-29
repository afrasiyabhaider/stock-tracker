<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth:sanctum'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

Route::get('/stock/{symbol}', function ($symbol) {
    $apiKey = config('services.stock_data_api_key'); // Ensure you have this key set in your config/services.php
    if (empty($apiKey)) {
        return $this->returnResponse('API key not configured', [], 500);
    }

    $url = "https://api.stockdata.org/v1/data/quote?symbols={$symbol}&api_token={$apiKey}";
    $response = Http::get($url); // Replace with actual JSON API endpoint
    // $data = $response->json();
    dd($url, $response->json());
    if ($response->successful()) {
        return response()->json($response->json());
    } else {
        return response()->json(['error' => 'Failed to fetch stock data'], $response->status());
    }
});
