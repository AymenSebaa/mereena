<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /*
    ALTER TABLE `items` ADD COLUMN `organization_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD CONSTRAINT `items_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE;

    ALTER TABLE `alerts`ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`;
    */
    public function up(): void {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
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
