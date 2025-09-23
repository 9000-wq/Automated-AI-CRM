<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Http;

class EmailApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call Email Api';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            // Example API call
            $response = Http::get(env('API_URL_ENDPOINT').'/endpoint');

            if ($response->successful()) {
                $this->info('API call successful: ' . $response->body());
            } else {
                $this->error('API call failed: ' . $response->status());
            }



        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

    }
}
