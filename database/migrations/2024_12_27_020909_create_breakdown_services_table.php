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
            $table->enum('location',['Sewing Line'])->default("Sewing Line");
            $table->text('breakdown_problem_note')->nullable();
            $table->enum('breakdown_service_status',['Pending','Processing','Done','Cancel'])->default("Pending");
            $table->enum('breakdown_service_technician_status',['Pending','Coming','Service Running','Success','Failed'])->default("Pending");
            $table->time('service_time')->nullable();
            $table->date('service_date')->nullable();
            $table->dateTime('technician_acknowledge_start_time')->nullable();
            $table->dateTime('technician_service_start_time')->nullable();
            $table->dateTime('technician_service_end_time')->nullable();
            $table->text('breakdown_technician_problem_note')->nullable();
            $table->decimal('parts_quantity')->default(0);
            

             // Foreign key assign
            $table->bigInteger('parts_id')->default(0);
            $table->bigInteger('line_id');
            $table->bigInteger('breakdown_problem_note_id')->default(0);
            $table->bigInteger('machine_id');
            $table->bigInteger('technician_id');
            $table->foreignId('company_id');

            // Foreign key References
            $table->foreign("company_id")
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('cascade');


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
        Schema::dropIfExists('breakdown_services');
    }
};