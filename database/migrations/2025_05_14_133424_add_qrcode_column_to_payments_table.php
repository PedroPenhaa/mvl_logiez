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
            // Adicionar a coluna qrcode como TEXT se ela não existir
            if (!Schema::hasColumn('payments', 'qrcode')) {
                $table->text('qrcode')->nullable()->after('payment_link');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remover a coluna qrcode se ela existir
            if (Schema::hasColumn('payments', 'qrcode')) {
                $table->dropColumn('qrcode');
            }
        });
    }
};
