<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoHistoricalDataTable extends Migration
{
    public function up()
    {
        Schema::create('crypto_historical_data', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->index(); // Cryptocurrency symbol (BTC, ETH, etc.)
            $table->decimal('price', 20, 8); // Price with 8 decimal places
            $table->decimal('market_cap', 30, 2)->nullable(); // Market capitalization
            $table->decimal('volume_24h', 30, 2)->nullable(); // Volume in 24h
            $table->decimal('percent_change_24h', 10, 2)->nullable(); // Percentage change in 24h
            $table->decimal('percent_change_7d', 10, 2)->nullable(); // Percentage change in 7 days
            $table->dateTime('recorded_at')->index(); // Time of registration
            $table->timestamps();

            // Single index to avoid duplicates
            $table->unique(['symbol', 'recorded_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('crypto_historical_data');
    }
}