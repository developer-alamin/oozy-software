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
        Schema::create('breakdown_services', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->dateTime('date_time');
            $table->enum('service_status',['Pending','Processing','Done','Cancel'])->default("Pending");

            $table->string("supervisor_problem_note_id")->nullable()->comment('for multiple id, note from supervisor'); 
            $table->string('supervisor_note')->nullable()->comment('note from supervisor');

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
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakdown_services');
    }
};
