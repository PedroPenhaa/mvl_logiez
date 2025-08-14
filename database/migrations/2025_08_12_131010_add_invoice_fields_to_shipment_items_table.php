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
        Schema::table('shipment_items', function (Blueprint $table) {
            // Campos específicos para invoice
            $table->decimal('unit_price_usd', 10, 2)->nullable()->after('unit_price')->comment('Preço unitário em USD');
            $table->decimal('total_price_usd', 10, 2)->nullable()->after('total_price')->comment('Preço total em USD');
            $table->string('unit_type', 20)->nullable()->after('harmonized_code')->comment('Tipo de unidade (PAR, KG, etc)');
            $table->string('ncm', 20)->nullable()->after('unit_type')->comment('Código NCM do produto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price_usd',
                'total_price_usd',
                'unit_type',
                'ncm'
            ]);
        });
    }
};
