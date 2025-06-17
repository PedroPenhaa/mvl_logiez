<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('profile_type', ['individual', 'business'])->default('individual');
            $table->string('document_number', 20)->nullable()->comment('CPF/CNPJ do usuário');
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('address_complement', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 2)->default('BR');
            $table->string('remember_token', 100)->nullable();
            $table->string('api_token', 100)->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        // Insert default admin user
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'pedro.eng98@gmail.com',
            'password' => Hash::make('123456'),
            'profile_type' => 'individual',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
