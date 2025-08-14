<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Campos específicos para invoice
            $table->decimal('freight_usd', 10, 2)->nullable()->after('total_price_brl')->comment('Valor do frete em USD');
            $table->integer('volumes')->nullable()->after('freight_usd')->comment('Número de volumes');
            $table->decimal('net_weight_lbs', 10, 4)->nullable()->after('volumes')->comment('Peso líquido em libras');
            $table->decimal('gross_weight_lbs', 10, 4)->nullable()->after('net_weight_lbs')->comment('Peso bruto em libras');
            $table->integer('container')->nullable()->after('gross_weight_lbs')->comment('Número de containers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'freight_usd',
                'volumes',
                'net_weight_lbs',
                'gross_weight_lbs',
                'container'
            ]);
        });
    }
};
