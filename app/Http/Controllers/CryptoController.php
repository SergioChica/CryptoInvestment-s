<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CryptoController extends Controller
{
    public function getCryptoPrices(Request $request) {
        $apikey = env('COINMARKETCAP_API_KEY');
        $symbols = $request->query('symbols', 'BTC,ETH,BNB'); // Valores por defecto si no selecciona nada
    
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apikey,
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
            'symbol' => $symbols, 'convert' => 'USD'
        ]);
    
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Error al obtener datos'], 500);
        }
    }
}