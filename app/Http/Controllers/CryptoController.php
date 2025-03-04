<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CryptoHistoricalData;

class CryptoController extends Controller
{
    public function getCryptoPrices(Request $request) {
        $apikey = env('COINMARKETCAP_API_KEY');
        
        $symbols = $request->query('symbols', 'ETH,USDT,XRP,BNB,SOL,USDC,ADA,DOGE,TRX,STETH,PI,HBAR,LEO,WSTETH,LINK,XLM,AVAX,USDS,LTC,TON,SHIB,SUI,OM,DOT,BCH,WETH,USDE,HYPE,BGB,WEETH,UNI,XMR,DAI,NEAR,APT,SUSDS,ONDO,PEPE,ICP,AAVE,ETC,GT,OKB,TRUMP,MNT,TKX');
    
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apikey,
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
            'symbol' => $symbols,
            'convert' => 'USD'
        ]);
    
        if ($response->successful()) {
            $responseData = $response->json();
            $cryptoData = $responseData['data'];
            
            // Guardar datos históricos
            CryptoHistoricalData::saveHistoricalData($cryptoData);
    
            return response()->json($cryptoData);
        } else {
            return response()->json(['error' => 'Error al obtener datos'], 500);
        }
    }

    public function getHistoricalData(Request $request)
    {
        $symbol = $request->query('symbol', 'BTC');
        $days = $request->query('days', 30);

        $historicalData = CryptoHistoricalData::getHistoricalDataBySymbol($symbol, $days);

        return response()->json($historicalData);
    }
}