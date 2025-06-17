<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_id', 100)->nullable()->comment('ID da transação no gateway');
            $table->string('payment_method', 50)->comment('Método de pagamento');
            $table->string('payment_gateway', 50)->default('asaas')->comment('Gateway de pagamento');
            $table->decimal('amount', 10, 2)->comment('Valor do pagamento');
            $table->string('currency', 3)->default('BRL');
            $table->string('status', 50)->default('pending')->comment('Status do pagamento');
            $table->timestamp('payment_date')->nullable()->comment('Data do pagamento');
            $table->timestamp('due_date')->nullable()->comment('Data de vencimento');
            $table->string('payer_name')->nullable();
            $table->string('payer_document', 20)->nullable()->comment('CPF/CNPJ do pagador');
            $table->string('payer_email')->nullable();
            $table->string('invoice_url', 512)->nullable()->comment('URL da fatura');
            $table->string('barcode')->nullable()->comment('Código de barras para pagamento');
            $table->string('payment_link', 512)->nullable()->comment('Link de pagamento');
            $table->json('gateway_response')->nullable()->comment('Resposta completa do gateway');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
} 