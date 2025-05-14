<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, adicionamos novas colunas com tamanho maior
        Schema::table('payments', function (Blueprint $table) {
            // Adicionar novas colunas com tipo TEXT
            $table->text('payment_link_text')->nullable()->after('payment_link');
            $table->text('barcode_text')->nullable()->after('barcode');
        });

        // Depois, copiamos os dados das colunas antigas para as novas
        DB::statement('UPDATE payments SET payment_link_text = payment_link, barcode_text = barcode');

        // Finalmente, removemos as colunas antigas e renomeamos as novas
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_link', 'barcode']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_link_text', 'payment_link');
            $table->renameColumn('barcode_text', 'barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Para reverter, seguimos o processo inverso
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_link_old', 255)->nullable()->after('payment_link');
            $table->string('barcode_old', 255)->nullable()->after('barcode');
        });

        // Copiar dados (possivelmente truncados)
        DB::statement('UPDATE payments SET payment_link_old = payment_link, barcode_old = barcode');

        // Remover colunas TEXT e renomear as novas
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_link', 'barcode']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_link_old', 'payment_link');
            $table->renameColumn('barcode_old', 'barcode');
        });
    }
};
