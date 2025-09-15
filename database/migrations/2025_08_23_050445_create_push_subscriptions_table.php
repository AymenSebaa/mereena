<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id(); // primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('push_ids')->nullable(); // store subscriptions as JSON
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('push_subscriptions');
    }
};
