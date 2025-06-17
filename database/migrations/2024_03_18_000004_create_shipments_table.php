<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tracking_number', 100)->nullable()->unique();
            $table->string('shipment_id', 100)->nullable()->comment('ID de referência do transportador');
            $table->string('carrier', 100)->default('FEDEX');
            $table->string('service_code', 100)->nullable();
            $table->string('service_name')->nullable();
            $table->string('label_url', 512)->nullable()->comment('URL da etiqueta');
            $table->string('label_format', 10)->default('PDF');
            $table->string('status', 50)->default('created')->comment('Status do envio');
            $table->string('status_description')->nullable();
            $table->timestamp('last_status_update')->nullable();
            $table->decimal('package_height', 10, 2)->comment('Altura em cm');
            $table->decimal('package_width', 10, 2)->comment('Largura em cm');
            $table->decimal('package_length', 10, 2)->comment('Comprimento em cm');
            $table->decimal('package_weight', 10, 2)->comment('Peso em kg');
            $table->decimal('total_price', 10, 2)->nullable()->comment('Valor total do frete');
            $table->string('currency', 3)->default('USD');
            $table->decimal('total_price_brl', 10, 2)->nullable()->comment('Valor total em BRL');
            $table->date('ship_date')->nullable()->comment('Data do envio');
            $table->date('estimated_delivery_date')->nullable()->comment('Data estimada de entrega');
            $table->date('delivery_date')->nullable()->comment('Data efetiva de entrega');
            $table->boolean('is_simulation')->default(false);
            $table->boolean('was_delivered')->default(false);
            $table->boolean('has_issues')->default(false);
            $table->timestamps();

            $table->index('tracking_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
} 