<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('label', 255);
            $table->string('endpoint', 255);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'label']);
            $table->index(['asset_id', 'endpoint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
