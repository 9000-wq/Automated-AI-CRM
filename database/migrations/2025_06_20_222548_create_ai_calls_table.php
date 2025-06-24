<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiCallsTable extends Migration
{
    public function up()
    {
        Schema::create('ai_calls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contact_id');
            $table->enum('direction', ['Inbound', 'Outbound']);
            $table->text('transcript')->nullable();
            $table->string('sentiment')->nullable();
            $table->enum('outcome', ['Converted', 'No Answer', 'Escalated'])->nullable();
            $table->text('audio_link')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->foreign('contact_id')
                  ->references('id')
                  ->on('contacts')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_calls');
    }
}
