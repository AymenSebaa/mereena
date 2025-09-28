<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    // php artisan migrate --path=moduels/Example/Database/Migrations/items_table.php
    public function up(): void {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type_id')->index();
            $table->string('name');
            $table->integer('price')->default(0);
            $table->text('desc')->nullable();
            $table->json('images')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('examples');
    }
};
