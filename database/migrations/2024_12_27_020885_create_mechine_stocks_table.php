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
        Schema::create('mechine_stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('type');
            $table->string('status')->default('in_stock');
            
             // Foreign key assign
            $table->foreignId('company_id');
            $table->foreignId('mechine_assing_id');

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
        Schema::dropIfExists('mechine_stocks');
    }
};