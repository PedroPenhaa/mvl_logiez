<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name')->nullable();
            $table->string('company_document', 20)->nullable()->comment('CNPJ da empresa');
            $table->string('state_registration', 20)->nullable()->comment('Inscrição estadual');
            $table->string('business_area', 100)->nullable();
            $table->string('shipping_volume', 50)->nullable()->comment('Volume estimado de envios');
            $table->string('default_sender_name')->nullable();
            $table->string('default_sender_address')->nullable();
            $table->string('default_sender_complement', 100)->nullable();
            $table->string('default_sender_city', 100)->nullable();
            $table->string('default_sender_state', 50)->nullable();
            $table->string('default_sender_postal_code', 20)->nullable();
            $table->string('default_sender_country', 2)->default('BR');
            $table->string('default_sender_phone', 20)->nullable();
            $table->string('default_sender_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
} 