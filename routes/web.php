<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('crypto-prices', [CryptoController::class, 'getCryptoPrices']);

Route::get('crypto', function () {
    return view('crypto');
});

