<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique(); // NEW
            $table->foreignId('hotel_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('comment')->nullable();
            $table->integer('priority')->default(1);
            $table->integer('status')->default(1);
            $table->string('invoice_number')->nullable();
            $table->string('pickup_address')->nullable();
            $table->decimal('pickup_address_lat', 10, 7)->nullable();
            $table->decimal('pickup_address_lng', 10, 7)->nullable();
            $table->dateTime('pickup_time_from')->nullable();
            $table->dateTime('pickup_time_to')->nullable();
            $table->string('delivery_address')->nullable();
            $table->decimal('delivery_address_lat', 10, 7)->nullable();
            $table->decimal('delivery_address_lng', 10, 7)->nullable();
            $table->dateTime('delivery_time_from')->nullable();
            $table->dateTime('delivery_time_to')->nullable();
            $table->string('distance')->nullable();
            $table->string('duration')->nullable();
            $table->longText('polyline')->nullable();
            $table->json('directions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('tasks');
    }
};
