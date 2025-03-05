<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\CryptoHistoricalData;

class FetchCryptoPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apikey = env('COINMARKETCAP_API_KEY');
        $symbols = 'ETH,USDT,XRP,BNB,SOL,USDC,ADA,DOGE,TRX,STETH,PI,HBAR,LEO,WSTETH,LINK,XLM,AVAX,USDS,LTC,TON,SHIB,SUI,OM,DOT,BCH,WETH,USDE,HYPE,BGB,WEETH,UNI,XMR,DAI,NEAR,APT,SUSDS,ONDO,PEPE,ICP,AAVE,ETC,GT,OKB,TRUMP,MNT,TKX';

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apikey,
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
            'symbol' => $symbols,
            'convert' => 'USD'
        ]);

        if ($response->successful()) {
            $cryptoData = $response->json()['data'];
            CryptoHistoricalData::saveHistoricalData($cryptoData);
        }
    }
}
