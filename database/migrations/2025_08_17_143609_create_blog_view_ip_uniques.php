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
        Schema::create('blog_view_ip_uniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onUpdate('cascade');
            $table->char('ip_hash', 64);
            $table->date('view_date');
            $table->timestamps();

            $table->unique(['blog_id', 'view_date', 'ip_hash'], 'uniq_blog_date_iphash');
            $table->index(['view_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_view_ip_uniques');
    }
};
