<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image');        // image URL or storage path
            $table->text('caption');        // post caption
            $table->text('hashtags');       // hashtags

            $table->string('fb_username');  
            $table->string('fb_password');  // ⚠️ for demo, better to use token

            $table->timestamp('schedule_time'); // when to post
            $table->enum('status', ['pending','scheduled','posted','failed'])->default('pending');
            $table->string('Link')->nullable(); // Facebook post link
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_posts');
    }
};
