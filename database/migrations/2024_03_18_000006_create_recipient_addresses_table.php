<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipientAddressesTable extends Migration
{
    public function up(): void
    {
        Schema::create('recipient_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('address_complement', 100)->nullable();
            $table->string('city', 100);
            $table->string('state', 50);
            $table->string('postal_code', 20);
            $table->string('country', 2);
            $table->boolean('is_residential')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipient_addresses');
    }
} 