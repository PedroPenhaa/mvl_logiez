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
        Schema::table('users', function (Blueprint $table) {
            // Adicionar colunas OAuth se não existirem
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable()->after('password');
            }
            
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable()->after('provider');
            }
            
            if (!Schema::hasColumn('users', 'profile_type')) {
                $table->enum('profile_type', ['individual', 'business'])->default('individual')->after('provider_id');
            }
            
            if (!Schema::hasColumn('users', 'document_number')) {
                $table->string('document_number', 20)->nullable()->after('profile_type');
            }
            
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('document_number');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address', 255)->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'address_number')) {
                $table->string('address_number', 20)->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('users', 'address_complement')) {
                $table->string('address_complement', 100)->nullable()->after('address_number');
            }
            
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 100)->nullable()->after('address_complement');
            }
            
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state', 50)->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('state');
            }
            
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 2)->default('BR')->after('postal_code');
            }
            
            if (!Schema::hasColumn('users', 'api_token')) {
                $table->string('api_token', 100)->unique()->nullable()->after('remember_token');
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('api_token');
            }
            
            if (!Schema::hasColumn('users', 'admin')) {
                $table->boolean('admin')->default(false)->after('is_active');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('admin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover colunas OAuth se existirem
            $columns = [
                'provider', 'provider_id', 'profile_type', 'document_number', 
                'phone', 'address', 'address_number', 'address_complement',
                'city', 'state', 'postal_code', 'country', 'api_token',
                'is_active', 'admin', 'last_login_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
