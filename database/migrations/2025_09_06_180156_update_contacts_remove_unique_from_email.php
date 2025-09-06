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
        Schema::table('contacts', function (Blueprint $table) {
            // Drop unique index from email
            $table->dropUnique(['email']);
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Restore unique index if rolled back
            $table->unique('email');
        });
    }
    
};
