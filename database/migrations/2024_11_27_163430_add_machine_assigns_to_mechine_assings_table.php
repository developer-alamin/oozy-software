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
        Schema::table('mechine_assings', function (Blueprint $table) {
          $table->string('serial_number')->nullable();
          $table->dateTime('commission_date')->nullable();
          $table->dateTime('commission_date')->nullable();
          $table->string('ownership')->nullable();
          $table->enum('power_requirements', ['Voltage','Amperage','Phase'])->default('Voltage');
          $table->string('capacity')->nullable();
          $table->enum('dimensions', ['Length','Width','Height'])->default('Length');
          $table->string('machine_weight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mechine_assings', function (Blueprint $table) {
            //
        });
    }
};
