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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('Assign');
            $table->text('meta_data')->nullable();
            
             // Foreign key assign
            $table->foreignId('company_id');
            $table->foreignId('mechine_assing_id');
            $table->foreignId('line_id');
            

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
        Schema::dropIfExists('movements');
    }
};