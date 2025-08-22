<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            Schema::table('companies', function (Blueprint $table) {
                $table->text('company_description')->nullable()->after('company_name'); 
                $table->text('bussiness_knowledge')->nullable()->after('company_description');
                $table->text('price_guidelines')->nullable()->after('bussiness_knowledge');
            });
        }

        public function down(): void
        {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn([
                    'company_description',
                    'bussiness_knowledge',
                    'price_guidelines'
                ]);
            });
        }
};
