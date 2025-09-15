<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('name');
            $table->unsignedBigInteger('type_id')->nullable(); 
            $table->string('location')->nullable();
            $table->json('geofence')->nullable(); 
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('types')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('zones');
    }
};
