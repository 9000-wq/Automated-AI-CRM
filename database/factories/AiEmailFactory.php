<?php

namespace Database\Factories;

use App\Models\AiEmail;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiEmailFactory extends Factory
{
    
    protected $model = AiEmail::class;

    public function definition()
    {
        // Pick a random status from your enum
        $status = $this->faker->randomElement(['Sent', 'Opened', 'Replied', 'Bounced']);

        // Created date (within last 30 days)
        $createdAt = $this->faker->dateTimeBetween('-30 days', 'now');

        // Opened date (only if status is Opened, Replied, or Bounced)
        $openedAt = in_array($status, ['Opened', 'Replied']) 
            ? $this->faker->dateTimeBetween($createdAt, 'now') 
            : null;

        // Clicked date (only possible if email was opened)
        $clickedAt = $openedAt && $this->faker->boolean(70) // 70% chance
            ? $this->faker->dateTimeBetween($openedAt, 'now') 
            : null;

        // Replied date (only possible if email was opened & status is Replied)
        $repliedAt = $status === 'Replied'
            ? $this->faker->dateTimeBetween($openedAt ?? $createdAt, 'now') 
            : null;

        return [
            'contact_id' => Contact::factory(),
            'sequence_id' => (string) $this->faker->numberBetween(1, 5),
            'content' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
            'subject' => $this->faker->sentence,
            'status' => $status,
            'opened_at' => $openedAt,
            'clicked_at' => $clickedAt,
            'replied_at' => $repliedAt,
            'created_at' => $createdAt,
        ];
    }

}