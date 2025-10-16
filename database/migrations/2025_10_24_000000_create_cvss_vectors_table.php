<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvss_vectors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('vector_string', 255)->unique();
            $table->string('attack_vector', 20);
            $table->string('attack_complexity', 20);
            $table->string('privileges_required', 20);
            $table->string('user_interaction', 20);
            $table->string('scope', 20);
            $table->string('confidentiality_impact', 20);
            $table->string('integrity_impact', 20);
            $table->string('availability_impact', 20);
            $table->decimal('base_score', 4, 1);
            $table->string('base_severity', 20);
            $table->timestamps();

            $table->index(['base_severity', 'base_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvss_vectors');
    }
};
