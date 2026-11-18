<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Jadwal yang sudah dipakai reservasi tidak boleh dihapus
            $table->foreignId('schedule_id')->constrained('visit_schedules')->restrictOnDelete();
            $table->string('booking_code', 40)->unique();
            $table->string('visitor_name');
            $table->string('phone', 30);
            $table->unsignedInteger('total_visitors');
            // pending / approved / rejected / cancelled
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['schedule_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
