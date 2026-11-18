<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date');
            $table->time('start_time');
            $table->time('end_time');
            // Harus lebih dari 0, dicek di Form Request
            $table->unsignedInteger('capacity');
            $table->boolean('is_available')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_schedules');
    }
};
