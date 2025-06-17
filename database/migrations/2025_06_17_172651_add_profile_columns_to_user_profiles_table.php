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
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'phone')) {
                $table->string('phone')->nullable()->comment('Número de telefone do usuário');
            }
            if (!Schema::hasColumn('user_profiles', 'company_name')) {
                $table->string('company_name')->nullable()->comment('Nome da empresa ou CPF/CNPJ');
            }
            if (!Schema::hasColumn('user_profiles', 'address')) {
                $table->string('address')->nullable()->comment('Endereço completo');
            }
            if (!Schema::hasColumn('user_profiles', 'city')) {
                $table->string('city')->nullable()->comment('Cidade');
            }
            if (!Schema::hasColumn('user_profiles', 'state')) {
                $table->string('state', 2)->nullable()->comment('Estado (UF)');
            }
            if (!Schema::hasColumn('user_profiles', 'zip_code')) {
                $table->string('zip_code')->nullable()->comment('CEP');
            }
            if (!Schema::hasColumn('user_profiles', 'country')) {
                $table->string('country', 2)->nullable()->comment('País (código ISO)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'company_name',
                'address',
                'city',
                'state',
                'zip_code',
                'country'
            ]);
        });
    }
};
