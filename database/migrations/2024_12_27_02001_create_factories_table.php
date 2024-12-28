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
        Schema::create('factories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
        
            $table->string('name'); // Name of the factory
            $table->string('factory_code')->nullable();
            $table->string('factory_owner')->nullable();
            $table->string('factory_size')->nullable();
            $table->string('factory_capacity')->nullable();
            $table->string('email')->nullable(); // Name of the factory manager
            $table->string('phone')->nullable(); // Contact number for the factory
            $table->string('location')->nullable();
            $table->text('note')->nullable(); // Total number of machines
            $table->text('meta_data')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');
            
            // Foreign key assign
            $table->foreignId('company_id');

             // Foreign key References
            $table->foreign("company_id")
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('cascade');


            $table->morphs('creator'); // Polymorphic relationship for creator
            $table->morphs('updater'); // Polymorphic relationship for updater
            
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
        Schema::dropIfExists('factories');
    }
};