<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingRatesTable extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('carrier', 100)->default('FEDEX');
            $table->string('service_code', 100);
            $table->string('service_name');
            $table->string('origin_country', 2);
            $table->string('destination_country', 2);
            $table->decimal('min_weight', 10, 2)->nullable()->comment('Peso mínimo em kg');
            $table->decimal('max_weight', 10, 2)->nullable()->comment('Peso máximo em kg');
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('price_per_kg', 10, 2)->nullable();
            $table->decimal('handling_fee', 10, 2)->nullable()->comment('Taxa de manuseio');
            $table->decimal('fuel_surcharge', 10, 2)->nullable()->comment('Sobretaxa de combustível');
            $table->string('currency', 3)->default('USD');
            $table->integer('delivery_time_min')->nullable()->comment('Tempo mínimo de entrega em dias');
            $table->integer('delivery_time_max')->nullable()->comment('Tempo máximo de entrega em dias');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['carrier', 'service_code']);
            $table->index(['origin_country', 'destination_country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
} 