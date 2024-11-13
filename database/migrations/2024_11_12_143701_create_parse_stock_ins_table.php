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
        Schema::create('parse_stock_ins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parse_id');
            $table->decimal('quantity_in')->default(0);
            $table->string('type')->default('Parse');
            $table->morphs('creator');
            $table->morphs('updater');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parse_stock_ins');
    }
};