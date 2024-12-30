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
            $table->foreignId('company_id');
            $table->foreignId('factory_id');
            $table->foreignId('line_id')->nullable();
            //$table->big('machine_id')->default(0);
            $table->foreignId('supplier_id')->nullable();
            $table->foreignId('machine_status_id')->nullable();
            $table->foreignId('brand_id')->nullable();
            $table->foreignId('product_model_id')->nullable();
            $table->foreignId('machine_type_id')->nullable();
            $table->foreignId('source_id')->nullable();
           

            // Foreign key References

            $table->foreign("company_id")
            ->references('id')
            ->on('companies')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("factory_id")
            ->references('id')
            ->on('factories')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign("line_id")
            ->references('id')
            ->on('lines')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreign("supplier_id")
            ->references('id')
            ->on('lines')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreign("machine_status_id")
            ->references('id')
            ->on('machine_statuses')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreign("brand_id")
            ->references('id')
            ->on('brands')
            ->onUpdate('cascade')
            ->onDelete('set null');
            
            $table->foreign("product_model_id")
            ->references('id')
            ->on('product_models')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreign("machine_type_id")
            ->references('id')
            ->on('mechine_types')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreign("source_id")
            ->references('id')
            ->on('sources')
            ->onUpdate('cascade')
            ->onDelete('set null');


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