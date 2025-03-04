<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoHistoricalData extends Model
{
    protected $table = 'crypto_historical_data';

    protected $fillable = [
        'symbol', 
        'price', 
        'market_cap', 
        'volume_24h', 
        'percent_change_24h', 
        'percent_change_7d', 
        'recorded_at'
    ];

    // Método para guardar datos históricos
    public static function saveHistoricalData($cryptoData)
{
    $recordedAt = now();

    foreach ($cryptoData as $symbol => $data) {
        // Verificar si los datos de USD están presentes
        if (isset($data['quote']['USD'])) {
            self::create([
                'symbol' => $symbol,
                'price' => $data['quote']['USD']['price'],
                'market_cap' => $data['quote']['USD']['market_cap'] ?? null,
                'volume_24h' => $data['quote']['USD']['volume_24h'] ?? null,
                'percent_change_24h' => $data['quote']['USD']['percent_change_24h'] ?? null,
                'percent_change_7d' => $data['quote']['USD']['percent_change_7d'] ?? null,
                'recorded_at' => $recordedAt
            ]);
        }
    }
}

    // Método para obtener datos históricos
    public static function getHistoricalDataBySymbol($symbol, $days = 30)
    {
        return self::where('symbol', $symbol)
            ->orderBy('recorded_at', 'desc')
            ->limit($days)
            ->get();
    }
}