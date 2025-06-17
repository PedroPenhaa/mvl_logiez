<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToShipmentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Adicionando colunas que faltam conforme o erro
            $table->string('tipo_envio')->after('carrier')->comment('Tipo do envio (venda, devolução, etc)');
            $table->string('tipo_pessoa')->after('tipo_envio')->comment('Tipo de pessoa (pf, pj)');
            
            // Adicionando outras colunas que podem ser úteis
            $table->string('invoice_number')->nullable()->after('tipo_pessoa')->comment('Número da nota fiscal');
            $table->decimal('invoice_value', 10, 2)->nullable()->after('invoice_number')->comment('Valor da nota fiscal');
            $table->string('invoice_key')->nullable()->after('invoice_value')->comment('Chave da nota fiscal');
            $table->text('notes')->nullable()->after('has_issues')->comment('Observações do envio');
            $table->json('additional_data')->nullable()->after('notes')->comment('Dados adicionais em formato JSON');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_envio',
                'tipo_pessoa',
                'invoice_number',
                'invoice_value',
                'invoice_key',
                'notes',
                'additional_data'
            ]);
        });
    }
} 