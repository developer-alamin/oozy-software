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
        Schema::create('requisition_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');


            $table->integer('mc');
            $table->foreignId('requisition_id');
            $table->foreignId('machine_type_id');


            // Foreign key References
            $table->foreign("requisition_id")
            ->references('id')
            ->on('requisitions')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("machine_type_id")
            ->references('id')
            ->on('mechine_types')
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
        Schema::dropIfExists('requisition_details');
    }
};
