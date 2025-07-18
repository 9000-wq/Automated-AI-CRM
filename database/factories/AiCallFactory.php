<?php

namespace Database\Factories;

use App\Models\AiCall;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiCallFactory extends Factory
{
    protected $model = AiCall::class;

    public function definition()
    {
        return [
            'contact_id' => \App\Models\Contact::factory(),
            'lead_id' => \App\Models\Lead::factory(),
            'direction' => $this->faker->randomElement(['Inbound', 'Outbound']),
            'transcript' => $this->faker->paragraph,
            'sentiment' => $this->faker->randomElement(['positive', 'neutral', 'negative']),
            'outcome' => $this->faker->randomElement(['Converted', 'No Answer', 'Escalated', null]),
            'audio_link' => 'https://example.com/audio/'.$this->faker->uuid.'.mp3',
        ];
    }
}