<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProofOfDeliveryTable extends Migration
{
    public function up(): void
    {
        Schema::create('proof_of_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('document_url', 512)->nullable()->comment('URL do comprovante');
            $table->string('document_type', 10)->default('PDF');
            $table->string('signed_by')->nullable()->comment('Nome de quem assinou');
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('request_date')->nullable()->comment('Data da solicitação');
            $table->timestamp('expiration_date')->nullable()->comment('Data de expiração do link');
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proof_of_delivery');
    }
} 