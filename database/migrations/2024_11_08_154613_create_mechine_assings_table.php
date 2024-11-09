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
            $table->uuid('uuid')->unique();
            $table->bigInteger('company_id');
            $table->bigInteger('factory_id');
            $table->bigInteger('brand_id');
            $table->bigInteger('model_id');
            $table->bigInteger('mechine_type_id');
            $table->bigInteger('mechine_source_id');
            $table->bigInteger('supplier_id')->nullable();
            $table->bigInteger('rent_id')->nullable();
            $table->string('name');
            $table->string('mechine_code')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('preventive_service_days')->nullable();
            $table->decimal('purchace_price')->default(0);
            $table->dateTime('purchase_date')->nullable();
            $table->dateTime('date')->nullable();
            $table->enum('status',['Preventive','Production','Breakdown','Under Maintenance','Loan','Idol','AsFactory','Scraped']);
            $table->text('note')->nullable();
            $table->morphs('creator');
            $table->morphs('updater');
            $table->softDeletes();
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
