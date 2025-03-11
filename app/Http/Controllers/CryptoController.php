<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CryptoHistoricalData;

class CryptoController extends Controller
{
    public function getCryptoPrices(Request $request) {
        $apikey = env('COINMARKETCAP_API_KEY');
        $cryptoSymbols = env('CRYPTO_SYMBOLS');
        
        $symbols = $request->query('symbols', $cryptoSymbols);
    
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apikey,
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
            'symbol' => $symbols,
            'convert' => 'USD'
        ]);
    
        if ($response->successful()) {
            $responseData = $response->json();
            $cryptoData = $responseData['data'];
            
            // Saving historical data
            CryptoHistoricalData::saveHistoricalData($cryptoData);
    
            return response()->json($cryptoData);
        } else {
            return response()->json(['error' => 'Error al obtener datos'], 500);
        }
    }

    public function getHistoricalData(Request $request)
    {
        $symbol = $request->query('symbol', 'BTC');
        $days = $request->query('days', 7);

        $historicalData = CryptoHistoricalData::where('symbol', $symbol)
            ->when($days > 0, function ($query) use ($days) {
                return $query->where('recorded_at', '>=', now()->subDays($days));
            })
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Debug logging
        \Log::info('Historical Data Retrieved', [
            'symbol' => $symbol,
            'days' => $days,
            'records_count' => $historicalData->count(),
            'first_record' => $historicalData->first(),
            'last_record' => $historicalData->last()
        ]);

        return response()->json($historicalData);
    }
}