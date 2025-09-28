<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    // php artisan migrate --path=/database/migrations/2025_09_17_000003_create_inventories_table.php
    public function up(): void {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('price')->default(0);
            $table->integer('quantity')->default(0);
            $table->date('made_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('batch')->nullable();
            $table->timestamps();
            $table->softDeletes(); // <-- add deleted_at column
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventories');
    }
};
