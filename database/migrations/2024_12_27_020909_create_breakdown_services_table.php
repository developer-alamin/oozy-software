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
        Schema::create('breakdown_services', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('machine_id');
            $table->bigInteger('technician_id');
            $table->enum('location',['Sewing Line'])->default("Sewing Line");
            $table->bigInteger('line_id');
            $table->bigInteger('breakdown_problem_note_id')->default(0);
            $table->text('breakdown_problem_note')->nullable();
            $table->enum('breakdown_service_status',['Pending','Processing','Done','Cancel'])->default("Pending");
            $table->enum('breakdown_service_technician_status',['Pending','Coming','Service Running','Success','Failed'])->default("Pending");
            $table->time('service_time')->nullable();
            $table->date('service_date')->nullable();
            $table->morphs('creator');
            $table->morphs('updater');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_services');
    }
};