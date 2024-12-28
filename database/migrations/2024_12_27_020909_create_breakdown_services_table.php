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
            $table->foreignId('company_id');
            $table->foreignId('mechine_assing_id');
            //$table->foreignId('parts_id')->default(0);
            $table->foreignId('line_id');
            $table->foreignId('breakdown_problem_note_id')->default(0);
            $table->foreignId('technician_id');
           

            // Foreign key References
            $table->foreign("company_id")
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("mechine_assing_id")
            ->references('id')
            ->on('mechine_assings')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("line_id")
            ->references('id')
            ->on('lines')
            ->onUpdate('restrict')
            ->onDelete('restrict');

            $table->foreign("breakdown_problem_note_id")
            ->references('id')
            ->on('break_down_problem_notes')
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
        Schema::dropIfExists('breakdown_services');
    }
};