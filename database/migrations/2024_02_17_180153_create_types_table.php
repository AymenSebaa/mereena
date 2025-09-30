<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * php artisan migrate --path=/database/migrations/2024_02_17_180153_create_types_table.php
	 * @return void
	 */
	public function up() {
		if (!Schema::hasTable('types')) {
			Schema::create('types', function (Blueprint $table) {
				$table->id();
				$table->foreignId('type_id')->nullable()->constrained('types')->cascadeOnDelete();
				$table->boolean('status')->default(1)->comment("0: deactive, 1: active");
				$table->string('name');
				$table->timestamps();
            	$table->softDeletes();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('types');
	}
};
