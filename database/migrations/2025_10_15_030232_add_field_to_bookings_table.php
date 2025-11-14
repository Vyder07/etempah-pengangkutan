<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('bookings', 'vehicle_name')) {
                $table->string('vehicle_name');
            }
            if (!Schema::hasColumn('bookings', 'vehicle_plate')) {
                $table->string('vehicle_plate')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'purpose')) {
                $table->text('purpose');
            }
            if (!Schema::hasColumn('bookings', 'destination')) {
                $table->string('destination');
            }
            if (!Schema::hasColumn('bookings', 'start_date')) {
                $table->dateTime('start_date');
            }
            if (!Schema::hasColumn('bookings', 'end_date')) {
                $table->dateTime('end_date');
            }
            if (!Schema::hasColumn('bookings', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            }
            if (!Schema::hasColumn('bookings', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'user_id', 'vehicle_name', 'vehicle_plate', 'purpose',
                'destination', 'start_date', 'end_date', 'status', 'notes'
            ]);
        });
    }
};
