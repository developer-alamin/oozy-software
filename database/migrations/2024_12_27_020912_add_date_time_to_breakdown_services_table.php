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
        Schema::table('breakdown_services', function (Blueprint $table) {
            $table->dateTime('technician_acknowledge_start_time')->nullable();
            $table->dateTime('technician_service_start_time')->nullable();
            $table->dateTime('technician_service_end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakdown_services', function (Blueprint $table) {
            $table->dropColumn('technician_acknowledge_start_time');
            $table->dropColumn('technician_service_start_time');
            $table->dropColumn('technician_service_end_time');
        });
    }
};