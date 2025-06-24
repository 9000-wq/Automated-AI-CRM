<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('direction');
            $table->dateTime('date_start');
            $table->dateTime('date_end');
            $table->integer('duration')->comment('Duration in seconds');
            $table->string('parent_type')->nullable();
            $table->string('parent_name')->nullable();
            $table->text('description')->nullable();
            $table->string('assigned_user_name')->nullable();
            $table->text('teams')->nullable();
            $table->text('users')->nullable();
            $table->text('contacts')->nullable();
            $table->text('leads')->nullable();
             $table->text('lead_id')->nullable();
            $table->text('company_id')->nullable();
            $table->text('transcript')->nullable();
            $table->string('sentiment')->nullable();
            $table->string('outcome')->nullable();
            $table->string('audio_link')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calls');
    }
};