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
        Schema::create('suppliers', function (Blueprint $table) {
             $table->id();
            $table->uuid('uuid')->unique();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('contact_person')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->enum( 'type', ['Mechine', 'Parse'])->default('Mechine');

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
        Schema::dropIfExists('suppliers');
    }
};
