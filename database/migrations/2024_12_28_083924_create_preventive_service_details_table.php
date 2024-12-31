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
        Schema::create('preventive_service_details', function (Blueprint $table) {
            $table->id();
            
            $table->enum('status',["Processing","Done","Cancel"])->default('Processing');
            $table->enum('technician_status',['Acknowledge','Acknowledged','Start Service','Done', 'Failed'])->nullable();
            $table->dateTime("acknowledge_date_time")->nullable();

            $table->dateTime('service_start_date_time')->nullable();
            $table->dateTime('service_end_date_time')->nullable();
            $table->string("problem_note_id")->nullable()->comment('for multiple id'); 
            $table->string('note')->nullable();
            $table->text('parts_info')->nullable()->comment('for parts id and qty');


            // Foreign key assign
            $table->foreignId('preventive_service_id');
            $table->foreignId('technician_id');

            // Foreign key References
            $table->foreign("preventive_service_id")
            ->references('id')
            ->on('preventive_services')
            ->onUpdate('cascade')
            ->onDelete('cascade');

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
        Schema::dropIfExists('preventive_service_details');
    }
};
