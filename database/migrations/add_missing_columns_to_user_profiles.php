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
            // Adiciona coluna phone se não existir
            if (!Schema::hasColumn('user_profiles', 'phone')) {
                $table->string('phone', 20)->nullable()->after('company_name');
            }
            
            // Adiciona coluna address se não existir
            if (!Schema::hasColumn('user_profiles', 'address')) {
                $table->string('address', 255)->nullable()->after('phone');
            }
            
            // Adiciona coluna city se não existir
            if (!Schema::hasColumn('user_profiles', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            
            // Adiciona coluna state se não existir
            if (!Schema::hasColumn('user_profiles', 'state')) {
                $table->string('state', 50)->nullable()->after('city');
            }
            
            // Adiciona coluna zip_code se não existir
            if (!Schema::hasColumn('user_profiles', 'zip_code')) {
                $table->string('zip_code', 20)->nullable()->after('state');
            }
            
            // Adiciona coluna country se não existir
            if (!Schema::hasColumn('user_profiles', 'country')) {
                $table->string('country', 2)->default('BR')->nullable()->after('zip_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Remove as colunas adicionadas se existirem
            $columns = ['phone', 'address', 'city', 'state', 'zip_code', 'country'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('user_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 