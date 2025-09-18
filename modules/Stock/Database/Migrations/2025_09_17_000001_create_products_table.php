<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    // php artisan migrate --path=/database/migrations/2025_09_17_000001_create_products_table.php
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->json('images')->nullable(); // multiple images
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
