<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_scores', function (Blueprint $table) {
$table->unsignedBigInteger('lead_id');
$table->unsignedBigInteger('contact_id');
            $table->integer('profile_score');
            $table->integer('engagement_score');
            $table->integer('intent_score');
            $table->integer('external_data_score');
            $table->integer('total_score');
            $table->enum('score_band', ['Cold', 'Warm', 'Hot', 'Very Hot']);
            $table->text('next_best_action')->nullable();
            $table->timestamp('last_scored_at')->nullable();
            $table->json('explanation')->nullable();
            $table->timestamps();

            $table->primary(['lead_id', 'contact_id']);
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
$table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_scores');
    }
};
