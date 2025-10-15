<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('target_id')->constrained('targets')->cascadeOnDelete();
            $table->foreignUuid('cvss_vector_id')->constrained('cvss_vectors')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('status', 20)->default('open');
            $table->text('description');
            $table->text('recommendation')->nullable();
            $table->timestamps();

            $table->index(['target_id', 'status']);
            $table->index('cvss_vector_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
