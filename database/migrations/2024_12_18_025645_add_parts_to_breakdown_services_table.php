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
        Schema::table('breakdown_services', function (Blueprint $table) {
            $table->text('breakdown_technician_problem_note')->nullable();
            $table->bigInteger('parts_id')->default(0);
            $table->decimal('parts_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakdown_services', function (Blueprint $table) {
            //
        });
    }
};
