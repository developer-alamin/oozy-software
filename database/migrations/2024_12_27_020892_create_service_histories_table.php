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
            $table->text('operator_mechine_problem_note');
            $table->time('operator_call_time');
            $table->text('technician_note')->nullable();
            $table->time('technician_arrive_time');
            $table->time('technician_working_time');
            $table->enum('technician_status', ['Pending', 'Running','Success','Failed'])->default('Pending');
            $table->text('meta_data')->nullable();
           

            // Foreign key assign

            $table->foreignId('company_id');
            $table->foreignId('service_id');



           // $table->bigInteger('service_id');
            // $table->foreignId('service_id')
            // ->constrained('services')
            // ->cascadeOnUpdate()
            // ->cascadeOnDelete();

            $table->foreignId('operator_id');
            $table->foreignId('technician_id');


            // Foreign key References
            $table->foreign("company_id")
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("service_id")
            ->references('id')
            ->on('services')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("operator_id")
            ->references('id')
            ->on('users')
            ->onUpdate('restrict')
            ->onDelete('restrict');

            $table->foreign("technician_id")
            ->references('id')
            ->on('users')
            ->onUpdate('restrict')
            ->onDelete('restrict');



            $table->morphs('creator');
            $table->morphs('updater');

            $table->timestamp('created_at')
            ->useCurrent();
            $table->timestamp('updated_at')
            ->useCurrent()
            ->useCurrentOnUpdate();
            $table->softDeletes();
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