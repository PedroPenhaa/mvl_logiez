<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingEventsTable extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->timestamp('event_date')->nullable();
            $table->string('event_type', 100)->nullable();
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 2)->nullable();
            $table->boolean('is_exception')->default(false)->comment('Indica se é um evento de exceção');
            $table->json('raw_data')->nullable()->comment('Dados brutos do evento');
            $table->timestamps();

            $table->index('event_date');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
} 