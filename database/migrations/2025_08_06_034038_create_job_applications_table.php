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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('carriers')->onUpdate('cascade');
            $table->json('resume_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->enum('status', ['applied', 'accepted', 'rejected'])->default('applied');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['carrier_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
