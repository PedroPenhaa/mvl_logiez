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
        Schema::table('shipments', function (Blueprint $table) {
            // Remover os valores padrão atuais
            $table->string('tipo_envio', 20)->default(null)->change();
            $table->string('tipo_pessoa', 2)->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Restaurar os valores padrão anteriores
            $table->string('tipo_envio', 20)->default('venda')->change();
            $table->string('tipo_pessoa', 2)->default('pf')->change();
        });
    }
}; 