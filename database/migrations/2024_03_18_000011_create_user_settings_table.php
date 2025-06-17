<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSettingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('notification_email')->default(true)->comment('Receber notificações por email');
            $table->boolean('notification_sms')->default(false)->comment('Receber notificações por SMS');
            $table->string('default_currency', 3)->default('BRL');
            $table->string('language', 5)->default('pt-BR');
            $table->string('timezone', 50)->default('America/Sao_Paulo');
            $table->string('dashboard_view', 20)->default('summary');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
} 