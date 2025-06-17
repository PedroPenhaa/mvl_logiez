<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('origin_postal_code', 20);
            $table->string('origin_country', 2)->default('BR');
            $table->string('destination_postal_code', 20);
            $table->string('destination_country', 2);
            $table->decimal('package_height', 10, 2)->comment('Altura em cm');
            $table->decimal('package_width', 10, 2)->comment('Largura em cm');
            $table->decimal('package_length', 10, 2)->comment('Comprimento em cm');
            $table->decimal('package_weight', 10, 2)->comment('Peso em kg');
            $table->decimal('cubic_weight', 10, 2)->nullable()->comment('Peso cúbico calculado');
            $table->string('carrier', 100)->default('FEDEX')->comment('Transportadora');
            $table->string('service_code', 100)->nullable()->comment('Código do serviço cotado');
            $table->string('service_name')->nullable()->comment('Nome do serviço');
            $table->integer('delivery_time_min')->nullable()->comment('Tempo mínimo de entrega em dias');
            $table->integer('delivery_time_max')->nullable()->comment('Tempo máximo de entrega em dias');
            $table->decimal('total_price', 10, 2)->nullable()->comment('Valor total do frete em USD');
            $table->decimal('base_price', 10, 2)->nullable()->comment('Preço base do frete');
            $table->decimal('tax_amount', 10, 2)->nullable()->comment('Valor de impostos');
            $table->decimal('additional_fee', 10, 2)->nullable()->comment('Taxas adicionais');
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 4)->nullable()->comment('Taxa de câmbio para BRL');
            $table->decimal('total_price_brl', 10, 2)->nullable()->comment('Valor total em BRL');
            $table->json('request_data')->nullable()->comment('Dados enviados para a API');
            $table->json('response_data')->nullable()->comment('Resposta completa da API');
            $table->boolean('is_simulation')->default(false);
            $table->string('quote_reference', 100)->nullable()->comment('Referência da cotação');
            $table->timestamp('expires_at')->nullable()->comment('Data de expiração da cotação');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('origin_postal_code');
            $table->index('destination_postal_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
} 