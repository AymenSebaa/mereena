<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('role_id')->unique();
            $table->string('category')->nullable();
            $table->unsignedBigInteger('country_id')->unique();
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->string('phone', 20)->nullable()->unique();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('category')->references('name')->on('types')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('set null');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('profiles');
    }
};
