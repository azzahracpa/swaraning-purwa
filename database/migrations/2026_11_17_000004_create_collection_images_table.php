<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_images', function (Blueprint $table) {
            $table->id();
            // Foto ikut terhapus kalau koleksinya dihapus
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            // Berisi path file di disk penyimpanan, bukan URL lengkap
            $table->string('image_url', 500);
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_images');
    }
};
