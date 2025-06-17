<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Adicionando colunas que faltam conforme o erro
            $table->string('payment_id')->nullable()->after('transaction_id')->comment('ID do pagamento no gateway');
            $table->string('qrcode')->nullable()->after('barcode')->comment('QR Code para pagamento PIX');
            
            // Adicionando outras colunas úteis do gateway de pagamento
            $table->decimal('net_value', 10, 2)->nullable()->after('amount')->comment('Valor líquido após taxas');
            $table->string('invoice_number')->nullable()->after('invoice_url')->comment('Número da fatura/NF');
            $table->string('external_reference')->nullable()->after('invoice_number')->comment('Referência externa');
            $table->string('credit_card_number')->nullable()->after('external_reference')->comment('Últimos dígitos do cartão');
            $table->string('credit_card_brand')->nullable()->after('credit_card_number')->comment('Bandeira do cartão');
            $table->timestamp('confirmed_date')->nullable()->after('payment_date')->comment('Data de confirmação');
            $table->timestamp('credit_date')->nullable()->after('confirmed_date')->comment('Data de crédito');
            $table->string('transaction_receipt_url')->nullable()->after('payment_link')->comment('URL do comprovante');
            $table->boolean('was_refunded')->default(false)->after('status')->comment('Se foi reembolsado');
            $table->json('refund_data')->nullable()->after('was_refunded')->comment('Dados do reembolso');
            $table->text('decline_reason')->nullable()->after('refund_data')->comment('Motivo de recusa');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_id',
                'qrcode',
                'net_value',
                'invoice_number',
                'external_reference',
                'credit_card_number',
                'credit_card_brand',
                'confirmed_date',
                'credit_date',
                'transaction_receipt_url',
                'was_refunded',
                'refund_data',
                'decline_reason'
            ]);
        });
    }
} 