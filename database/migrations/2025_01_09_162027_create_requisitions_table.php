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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            
            $table->dateTime('startDate');
            $table->dateTime('endDate');
            $table->string('style');
            $table->integer('total');


            // Foreign key assign
            $table->foreignId('company_id');
            $table->foreignId('line_id');

             // Foreign key References
             $table->foreign("company_id")
             ->references('id')
             ->on('companies')
             ->onUpdate('cascade')
             ->onDelete('cascade');

            $table->foreign("line_id")
            ->references('id')
            ->on('lines')
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
        Schema::dropIfExists('requisitions');
    }
};
