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
            $table->string('name'); // Name of the factory
            $table->string('location')->nullable(); // Physical location of the factory
            $table->string('manager_name'); // Name of the factory manager
            $table->string('contact_number')->nullable(); // Contact number for the factory
            $table->integer('total_machines')->default(0); // Total number of machines
            $table->integer('employee_count')->default(0); // Number of employees
            $table->decimal('area_size', 8, 2)->nullable(); // Area size in square meters
            $table->boolean('is_active')->default(true); // Status of the factory (active/inactive)
            $table->text('notes')->nullable(); 
            $table->morphs('creator'); // Polymorphic relationship for creator
            $table->morphs('updater'); // Polymorphic relationship for updater
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
