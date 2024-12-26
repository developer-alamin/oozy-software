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
        Schema::create('mechine_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('day')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending'])->default('Inactive')->nullable();
            $table->string('partial_maintenance_day')->nullable();
            $table->string('full_maintenance_day')->nullable();
           
            // Foreign key assign
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
        Schema::dropIfExists('mechine_types');
    }
};
