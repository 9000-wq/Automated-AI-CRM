<?php

namespace Database\Seeders;

use App\Models\AiCall;
use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Database\Seeder;

class AiCallsTableSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have some contacts and leads first
        $contacts = Contact::pluck('id')->toArray();
        $leads = Lead::pluck('id')->toArray();

        if (empty($contacts)) {
            $contacts = [Contact::factory()->create()->id];
        }

        if (empty($leads)) {
            $leads = [Lead::factory()->create()->id];
        }

        $callData = [
            [
                'contact_id' => $contacts[array_rand($contacts)],
                'lead_id' => $leads[array_rand($leads)],
                'direction' => 'Inbound',
                'transcript' => 'Hello, I\'m interested in your product. Can you tell me more about it?',
                'sentiment' => 'positive',
                'outcome' => 'Converted',
                'audio_link' => 'https://example.com/audio/1.mp3',
            ],
            [
                'contact_id' => $contacts[array_rand($contacts)],
                'lead_id' => null, // Some calls might not have leads
                'direction' => 'Outbound',
                'transcript' => 'We were calling about your recent inquiry. Please call us back.',
                'sentiment' => 'neutral',
                'outcome' => 'No Answer',
                'audio_link' => 'https://example.com/audio/2.mp3',
            ],
            // Add more sample calls as needed
        ];

        foreach ($callData as $call) {
            AiCall::create($call);
        }

        // Or use factories for random data
        AiCall::factory()->count(10)->create();
    }
}