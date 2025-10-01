<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::table('lead_contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('lead_contacts', function (Blueprint $table) {
            // Replace NULL values before enforcing NOT NULL
            DB::table('lead_contacts')
                ->whereNull('account_id')
                ->update(['account_id' => 0]); // or a valid account_id from accounts table

            $table->unsignedBigInteger('account_id')->default(0)->nullable(false)->change();
        });
    }
};
