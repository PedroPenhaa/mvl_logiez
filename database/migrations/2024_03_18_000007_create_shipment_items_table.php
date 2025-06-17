<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('weight', 10, 2)->nullable()->comment('Peso em kg');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('country_of_origin', 2)->default('BR')->comment('País de origem do produto');
            $table->string('harmonized_code', 20)->nullable()->comment('Código NCM/Harmonizado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
} 