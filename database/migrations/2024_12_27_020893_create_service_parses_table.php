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
        Schema::create('service_parses', function (Blueprint $table) {
            $table->id();
            
            $table->decimal('use_qty')->default(0);
           
            // Foreign key assign
            $table->foreignId('company_id');
            $table->foreignId('service_id');
            $table->foreignId('parse_id');
            

            // Foreign key References
            $table->foreign("company_id")
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("service_id")
            ->references('id')
            ->on('services')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("parse_id")
            ->references('id')
            ->on('parses')
            ->onUpdate('cascade')
            ->onDelete('cascade');


            $table->morphs('creator');
            $table->morphs('updater');

            $table->timestamp('created_at')
            ->useCurrent();
            $table->timestamp('updated_at')
            ->useCurrent()
            ->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_parses');
    }
};
