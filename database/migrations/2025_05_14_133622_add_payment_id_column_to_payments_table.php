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
        Schema::table('payments', function (Blueprint $table) {
            // Adicionar a coluna payment_id como VARCHAR se ela não existir
            if (!Schema::hasColumn('payments', 'payment_id')) {
                $table->string('payment_id', 100)->nullable()->after('transaction_id')
                    ->comment('ID do pagamento no gateway (ex: Asaas)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remover a coluna payment_id se ela existir
            if (Schema::hasColumn('payments', 'payment_id')) {
                $table->dropColumn('payment_id');
            }
        });
    }
};
