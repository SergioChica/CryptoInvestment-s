<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CryptoController extends Controller
{
    public function getCryptoPrices() {
        $apikey = env('COINMARKETCAP_API_KEY');
        // $url = env('COINMARKETCAP_BASEURL_PRICES');C

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apikey,
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest', [
            'convert' => 'USD',
            'limit' => 10 //Numero de cripto a obtener
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error'=> 'Error al obtener datos', 500]);
        }
    }
}