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
        Schema::table('mechine_assings', function (Blueprint $table) {
            $table->boolean('show_basic_details')->default(false);
            $table->boolean('show_specifications')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mechine_assings', function (Blueprint $table) {
            $table->dropColumn('show_basic_details');
            $table->dropColumn('show_specifications');

        });
    }
};