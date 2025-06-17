<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('api_service', 50)->comment('Nome do serviço de API');
            $table->string('endpoint');
            $table->string('http_method', 10);
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('response_code')->nullable();
            $table->decimal('execution_time', 10, 3)->nullable()->comment('Tempo de execução em segundos');
            $table->string('status', 20)->default('success');
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('api_service');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
} 