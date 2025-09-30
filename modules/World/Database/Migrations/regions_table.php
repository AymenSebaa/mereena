<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('continent_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('m49_code')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('regions');
    }
};
