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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('appointment_slot', 30)->nullable()->after('appointment_date');
            $table->text('notes')->nullable()->after('appointment_slot');
            $table->index(['appointment_date', 'appointment_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['appointment_date', 'appointment_slot']);
            $table->dropColumn(['appointment_slot', 'notes']);
        });
    }
};
