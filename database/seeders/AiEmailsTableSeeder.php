<?php

namespace Database\Seeders;

use App\Models\AiEmail;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class AiEmailsTableSeeder extends Seeder
{
    public function run()
    {
        // Get or create contacts
        $contacts = Contact::pluck('id')->toArray();
        if (empty($contacts)) {
            $contacts = [Contact::factory()->create()->id];
        }

        // Sample email data
        $emails = [
            [
                'contact_id' => $contacts[array_rand($contacts)],
                'sequence_id' => 1,
                'content' => '<p>Thank you for your interest in our product.</p>',
                'subject' => 'Welcome to Our Service',
                'status' => 'sent',
                'opened_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'clicked_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'replied_at' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'contact_id' => $contacts[array_rand($contacts)],
                'sequence_id' => 2,
                'content' => '<p>Here is your special offer!</p>',
                'subject' => 'Special Discount',
                'status' => 'sent',
                'opened_at' => date('Y-m-d H:i:s', strtotime('-5 hours')),
                'clicked_at' => null,
                'replied_at' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'contact_id' => $contacts[array_rand($contacts)],
                'sequence_id' => 1,
                'content' => '<p>We noticed you left items in your cart.</p>',
                'subject' => 'Your Cart Items',
                'status' => 'draft',
                'opened_at' => null,
                'clicked_at' => null,
                'replied_at' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert emails
        foreach ($emails as $email) {
            AiEmail::create($email);
        }

        // Create additional random emails
        $this->createRandomEmails(7, $contacts);
    }

    protected function createRandomEmails($count, $contacts)
    {
        for ($i = 0; $i < $count; $i++) {
            $status = rand(0, 1) ? 'sent' : 'draft';
            $opened = $status === 'sent' ? date('Y-m-d H:i:s', strtotime('-'.rand(1, 48).' hours')) : null;
            
            AiEmail::create([
                'contact_id' => $contacts[array_rand($contacts)],
                'sequence_id' => rand(1, 5),
                'content' => '<p>'.implode('</p><p>', array_fill(0, 3, 'Sample content paragraph '.($i+1))).'</p>',
                'subject' => 'Email Subject '.($i+1),
                'status' => $status,
                'opened_at' => $opened,
                'clicked_at' => $opened && rand(0, 1) ? date('Y-m-d H:i:s', strtotime($opened) + rand(60, 3600)) : null,
                'replied_at' => $opened && rand(0, 1) ? date('Y-m-d H:i:s', strtotime($opened) + rand(3600, 86400)) : null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-'.rand(0, 30).' days'))
            ]);
        }
    }
}