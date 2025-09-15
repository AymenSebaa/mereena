<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('external_id')->unique(); // to avoid duplicates
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('device_id')->nullable();
            $table->unsignedBigInteger('geofence_id')->nullable();
            $table->string('type');
            $table->string('message')->nullable();
            $table->text('detail')->nullable();
            $table->text('address')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('altitude')->nullable();
            $table->double('course')->nullable();
            $table->double('speed')->nullable();
            $table->timestamp('time')->nullable();
            $table->json('additional')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('events');
    }
};
