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
        Schema::create('site_view_ip_uniques', function (Blueprint $table) {
            $table->id();
            $table->char('ip_hash', 64);
            $table->date('visit_date');
            $table->timestamps();

            $table->unique(['visit_date', 'ip_hash'], 'uniq_visit_date_iphash');
            $table->index(['visit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_view_ip_uniques');
    }
};
