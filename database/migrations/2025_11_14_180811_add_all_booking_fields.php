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
            // Add columns only if they don't exist
            $columns = Schema::getColumnListing('bookings');

            if (!in_array('user_id', $columns)) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!in_array('vehicle_name', $columns)) {
                $table->string('vehicle_name')->nullable();
            }
            if (!in_array('vehicle_plate', $columns)) {
                $table->string('vehicle_plate')->nullable();
            }
            if (!in_array('purpose', $columns)) {
                $table->text('purpose')->nullable();
            }
            if (!in_array('destination', $columns)) {
                $table->string('destination')->nullable();
            }
            if (!in_array('start_date', $columns)) {
                $table->dateTime('start_date')->nullable();
            }
            if (!in_array('end_date', $columns)) {
                $table->dateTime('end_date')->nullable();
            }
            if (!in_array('status', $columns)) {
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            }
            if (!in_array('notes', $columns)) {
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
            $columns = ['user_id', 'vehicle_name', 'vehicle_plate', 'purpose',
                       'destination', 'start_date', 'end_date', 'status', 'notes'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
