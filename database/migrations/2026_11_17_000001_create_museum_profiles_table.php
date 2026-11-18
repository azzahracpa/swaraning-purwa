<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Profil Museum Mpu Purwa. Hanya ada satu baris data utama.
     * Sesuai ERD hanya punya kolom updated_at (tanpa created_at).
     */
    public function up(): void
    {
        Schema::create('museum_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('history')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('museum_profiles');
    }
};
