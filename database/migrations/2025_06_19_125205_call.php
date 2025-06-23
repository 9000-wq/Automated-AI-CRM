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
        Schema::create('call', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('status');
            $table->string('direction');

            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('duration')->nullable();

            $table->string('parent_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->index(['parent_type', 'parent_id']);

            $table->text('description')->nullable();

            $table->unsignedBigInteger('assigned_user_id');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('lead_id')->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call');
    }
};