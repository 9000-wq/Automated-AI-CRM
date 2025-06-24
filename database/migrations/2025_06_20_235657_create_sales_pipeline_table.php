<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_pipeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->enum('stage', ['Created', 'Contacted', 'Proposal Sent', 'Closed'])->default('Created');
            $table->integer('probability')->nullable(); // % between 0–100
            $table->text('ai_notes')->nullable();
            $table->enum('outcome', ['Won', 'Lost', 'Escalated'])->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_pipeline');
    }
};
