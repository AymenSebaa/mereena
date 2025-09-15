<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('type', 30)->index();
            $table->unsignedBigInteger('type_id')->index();
            $table->string('extra')->nullable()->index(); // new extra field
            $table->longText('content')->collation('utf8mb4_bin');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['type', 'type_id', 'user_id', 'created_at'], 'unique_scan'); // prevent duplicates
        });
    }

    public function down(): void {
        Schema::dropIfExists('scans');
    }
};
