<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->renameColumn('parent_id', 'lead_id');
        });
    }

    public function down()
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->renameColumn('lead_id', 'parent_id');
        });
    }
};
