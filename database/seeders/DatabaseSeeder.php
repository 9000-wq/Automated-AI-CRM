<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // Other seeders you might have
            AiCallsTableSeeder::class,
            AiEmailsTableSeeder::class,
        ]);
    }
}