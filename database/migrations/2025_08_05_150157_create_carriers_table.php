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
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_category_id')->constrained('carrier_categories')->onUpdate('cascade');
            $table->foreignId('employee_status_id')->constrained('employee_statuses')->onUpdate('cascade');
            $table->foreignId('job_location_id')->constrained('job_locations')->onUpdate('cascade');
            $table->json('qualification');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
