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
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thread_id');
            $table->string('message_id')->unique();
            $table->string('from_email')->nullable();
            $table->string('to_email')->nullable();
            $table->text('subject')->nullable();
            $table->text('snippet')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('is_auto_reply')->default(false);
            $table->enum('status', ['Sent', 'Opened', 'Replied', 'Bounced'])->default('Sent');
            $table->timestamp('created_at')->useCurrent();

            // Foreign key
            $table->foreign('thread_id')
                  ->references('id')   // 🔧 usually FK references `id` not `thread_id`
                  ->on('email_threads')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};
