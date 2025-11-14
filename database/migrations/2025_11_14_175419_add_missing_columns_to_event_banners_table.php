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
        Schema::table('event_banners', function (Blueprint $table) {
            // Add missing columns without specifying position
            if (!Schema::hasColumn('event_banners', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('event_banners', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('event_banners', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('event_banners', 'order')) {
                $table->integer('order')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_banners', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'is_active', 'order']);
        });
    }
};
