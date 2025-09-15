<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('name');
            $table->string('device_name')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('speed', 5, 2)->nullable();
            $table->string('status')->nullable();
            $table->timestamp('eta')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('types')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('buses');
    }
};
