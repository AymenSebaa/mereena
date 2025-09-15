<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_fr');
            $table->string('name_ar');
            $table->string('code', 2); // ISO 3166-1 alpha-2
            $table->string('phone');
            $table->string('flag', 10);
            $table->decimal('lat', 10, 6);
            $table->decimal('lng', 10, 6);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('countries');
    }
};

/*
CREATE TABLE african_countries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(255) NOT NULL,
    name_fr VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255) NOT NULL,
    iso2 CHAR(2) NOT NULL,       -- e.g. DZ
    iso3 CHAR(3) NOT NULL,       -- e.g. DZA
    phone_code VARCHAR(20) NOT NULL,
    flag VARCHAR(255) NOT NULL,  -- filename or URL
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/