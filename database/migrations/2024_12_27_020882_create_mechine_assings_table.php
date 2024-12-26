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
        Schema::create('mechine_assings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->bigInteger('factory_id');
            $table->bigInteger('brand_id');
            $table->bigInteger('model_id');
            $table->bigInteger('machine_type_id');
            $table->bigInteger('machine_source_id');
            $table->string('machine_code');
            $table->string('name');
            $table->bigInteger('supplier_id')->nullable();
            $table->string('rent_name')->nullable();
            $table->string('rent_amount_type')->nullable();
            $table->text('rent_note')->nullable();
            $table->dateTime('rent_date')->nullable();
            $table->string('partial_maintenance_day')->nullable();
            $table->string('full_maintenance_day')->nullable();
            $table->decimal('purchase_price')->default(0);
            $table->dateTime('purchase_date')->nullable();
            $table->string('status')->default('Assign');
            $table->bigInteger('machine_status_id');
            $table->text('note')->nullable();
            $table->morphs('creator');
            $table->morphs('updater');
            $table->softDeletes();
            $table->text('meta_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mechine_assings');
    }
};