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
            $table->string('company_id');
            $table->string('name'); // Name of the factory
            $table->string('factory_code');
            $table->string('email')->nullable(); // Name of the factory manager
            $table->string('phone')->nullable(); // Contact number for the factory
            $table->string('location')->nullable();
            $table->integer('description')->default(0); // Total number of machines
            $table->text('meta_data')->nullable();
            $table->morphs('creator'); // Polymorphic relationship for creator
            $table->morphs('updater'); // Polymorphic relationship for updater
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');
            $table->timestamps();
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