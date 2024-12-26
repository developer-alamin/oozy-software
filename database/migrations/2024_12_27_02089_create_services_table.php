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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->time('service_time');
            $table->date('service_date');
            $table->enum('service_type_status', ['Preventive', 'Breakdown'])->default('Preventive');
            $table->text('description')->nullable();
            $table->text('meta_data')->nullable();
           
           // Foreign key assign
            $table->foreignId('company_id');
            $table->bigInteger('mechine_id');


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
        Schema::dropIfExists('services');
    }
};