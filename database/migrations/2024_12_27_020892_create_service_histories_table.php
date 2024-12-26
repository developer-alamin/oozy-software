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
        Schema::create('service_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('creator');
            $table->morphs('updater');
            $table->bigInteger('service_id');
            $table->bigInteger('operator_id');
            $table->text('operator_mechine_problem_note');
            $table->time('operator_call_time');
            $table->bigInteger('technician_id');
            $table->text('technician_note')->nullable();
            $table->time('technician_arrive_time');
            $table->time('technician_working_time');
            $table->enum('technician_status', ['Pending', 'Running','Success','Failed'])->default('Pending');
            $table->text('meta_data')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_histories');
    }
};