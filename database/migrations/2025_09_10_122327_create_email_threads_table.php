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
        Schema::create('email_threads', function (Blueprint $table) {
            $table->id();
            $table->string('thread_id')->unique();
            $table->string('last_message_id')->nullable();

            // Foreign keys
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();

            $table->boolean('auto_reply')->default(false);
            $table->timestamps();

            // Define FKs
            $table->foreign('lead_id')
                  ->references('id')->on('leads')
                  ->cascadeOnDelete();

            $table->foreign('contact_id')
                  ->references('id')->on('contacts')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_threads', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['contact_id']);
        });
        Schema::dropIfExists('email_threads');
    }
};
