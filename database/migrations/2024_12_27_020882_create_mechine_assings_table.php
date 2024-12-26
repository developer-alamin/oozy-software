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
            $table->string('machine_code');
            $table->string('name');
            $table->string('rent_name')->nullable();
            $table->string('rent_amount_type')->nullable();
            $table->text('rent_note')->nullable();
            $table->dateTime('rent_date')->nullable();
            $table->string('partial_maintenance_day')->nullable();
            $table->string('full_maintenance_day')->nullable();
            $table->decimal('purchase_price')->default(0);
            $table->dateTime('purchase_date')->nullable();
            $table->string('status')->default('Assign');
            $table->text('note')->nullable();
            $table->text('qr_code_path')->nullable();
            $table->string('serial_number')->nullable();
            $table->dateTime('commission_date')->nullable();
            $table->dateTime('warranty_period')->nullable();
            $table->string('ownership')->nullable();
            $table->enum('power_requirements', ['Voltage','Amperage','Phase'])->default('Voltage');
            $table->string('capacity')->nullable();
            $table->enum('dimensions', ['Length','Width','Height'])->default('Length');
            $table->string('machine_weight')->nullable();
            $table->string('material_compatibility')->nullable();
            $table->string('maximum_speed')->nullable();
            $table->string('optimum_speed')->nullable();
            $table->string('operating_temperature_range')->nullable();
            $table->enum('location_status', ['Out of Factory','Sewing Line','Idle Storage'])->default('Idle Storage');
            $table->string('tag')->nullable();
            $table->boolean('show_basic_details')->default(false);
            $table->boolean('show_specifications')->default(false);
            $table->softDeletes();
            $table->text('meta_data')->nullable();
           


             // Foreign key assign
            $table->bigInteger('line_id')->nullable();
            $table->bigInteger('machine_id')->default(0);
            $table->bigInteger('supplier_id')->nullable();
            $table->bigInteger('machine_status_id');
            $table->bigInteger('factory_id');
            $table->bigInteger('brand_id');
            $table->bigInteger('model_id');
            $table->bigInteger('machine_type_id');
            $table->bigInteger('machine_source_id');
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