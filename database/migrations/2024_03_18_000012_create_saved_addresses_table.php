<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedAddressesTable extends Migration
{
    public function up(): void
    {
        Schema::create('saved_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('address_type', ['sender', 'recipient']);
            $table->string('nickname', 100)->nullable()->comment('Nome amigável para o endereço');
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('address_complement', 100)->nullable();
            $table->string('city', 100);
            $table->string('state', 50);
            $table->string('postal_code', 20);
            $table->string('country', 2);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_residential')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'address_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_addresses');
    }
} 