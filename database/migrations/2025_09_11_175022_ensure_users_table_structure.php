<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Garante que a tabela users tenha exatamente a estrutura especificada
     */
    public function up(): void
    {
        // Verifica se a tabela já tem a estrutura correta
        if (Schema::hasTable('users')) {
            $columns = Schema::getColumnListing('users');
            $requiredColumns = [
                'id', 'name', 'email', 'email_verified_at', 'password', 'provider', 'provider_id',
                'profile_type', 'document_number', 'phone', 'address', 'address_number',
                'address_complement', 'city', 'state', 'postal_code', 'country',
                'remember_token', 'api_token', 'is_active', 'admin', 'last_login_at',
                'created_at', 'updated_at'
            ];
            
            // Se todas as colunas necessárias existem, não recria a tabela
            if (count(array_diff($requiredColumns, $columns)) === 0) {
                echo "Tabela users já possui a estrutura correta.\n";
                return;
            }
        }
        
        // Recria a tabela users com a estrutura exata
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            // id - bigint(20) unsigned, NO, PRI, NULL, auto_increment
            $table->id();
            
            // name - varchar(255), NO, '', NULL, ''
            $table->string('name');
            
            // email - varchar(255), NO, UNI, NULL, ''
            $table->string('email')->unique();
            
            // email_verified_at - timestamp, YES, '', NULL, ''
            $table->timestamp('email_verified_at')->nullable();
            
            // password - varchar(255), NO, '', NULL, ''
            $table->string('password');
            
            // provider - varchar(255), YES, '', NULL, ''
            $table->string('provider')->nullable();
            
            // provider_id - varchar(255), YES, '', NULL, ''
            $table->string('provider_id')->nullable();
            
            // profile_type - enum('individual','business'), NO, '', 'individual', ''
            $table->enum('profile_type', ['individual', 'business'])->default('individual');
            
            // document_number - varchar(20), YES, '', NULL, ''
            $table->string('document_number', 20)->nullable();
            
            // phone - varchar(20), YES, '', NULL, ''
            $table->string('phone', 20)->nullable();
            
            // address - varchar(255), YES, '', NULL, ''
            $table->string('address')->nullable();
            
            // address_number - varchar(20), YES, '', NULL, ''
            $table->string('address_number', 20)->nullable();
            
            // address_complement - varchar(100), YES, '', NULL, ''
            $table->string('address_complement', 100)->nullable();
            
            // city - varchar(100), YES, '', NULL, ''
            $table->string('city', 100)->nullable();
            
            // state - varchar(50), YES, '', NULL, ''
            $table->string('state', 50)->nullable();
            
            // postal_code - varchar(20), YES, '', NULL, ''
            $table->string('postal_code', 20)->nullable();
            
            // country - varchar(2), NO, '', 'BR', ''
            $table->string('country', 2)->default('BR');
            
            // remember_token - varchar(100), YES, '', NULL, ''
            $table->string('remember_token', 100)->nullable();
            
            // api_token - varchar(100), YES, UNI, NULL, ''
            $table->string('api_token', 100)->unique()->nullable();
            
            // is_active - tinyint(1), NO, '', '1', ''
            $table->boolean('is_active')->default(true);
            
            // admin - tinyint(1), NO, '', '0', ''
            $table->boolean('admin')->default(false);
            
            // last_login_at - timestamp, YES, '', NULL, ''
            $table->timestamp('last_login_at')->nullable();
            
            // created_at - timestamp, YES, '', NULL, ''
            // updated_at - timestamp, YES, '', NULL, ''
            $table->timestamps();
        });
        
        // Inserir usuário admin padrão se não existir
        $adminExists = DB::table('users')->where('email', 'pedro.eng98@gmail.com')->exists();
        
        if (!$adminExists) {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'pedro.eng98@gmail.com',
                'password' => bcrypt('123456'),
                'profile_type' => 'individual',
                'is_active' => true,
                'admin' => true,
                'country' => 'BR',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};