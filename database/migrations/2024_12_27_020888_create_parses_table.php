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
        Schema::create('parses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('supplier_name');
            $table->string('brand_name');
            $table->string('model_name');
            $table->string('name');
            $table->decimal('quantity')->default(0);
            $table->decimal('purchase_price')->default(0);
            $table->dateTime('purchase_date')->nullable();
            $table->enum('status',['Active','Inactive']);
            $table->text('note')->nullable();
            $table->text('meta_data')->nullable();
            

             // Foreign key assign
            $table->bigInteger('parse_unit_id')->nullable();
            $table->bigInteger('factory_id');
            $table->bigInteger('category_id');
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
        Schema::dropIfExists('parses');
    }
};