<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/crypto-prices', [CryptoController::class, 'getCryptoPrices']);
Route::get('/crypto-historical-data', [CryptoController::class, 'getHistoricalData']);

Route::get('crypto', function () {
    return view('crypto');
});
