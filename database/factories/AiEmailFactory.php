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
        $status = $this->faker->randomElement(['sent', 'draft']);
        $opened = $status === 'sent' ? date('Y-m-d H:i:s', strtotime('-'.$this->faker->numberBetween(1, 48).' hours')) : null;
        
        return [
            'contact_id' => Contact::factory(),
            'sequence_id' => $this->faker->numberBetween(1, 5),
            'content' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
            'subject' => $this->faker->sentence,
            'status' => $status,
            'opened_at' => $opened,
            'clicked_at' => $opened && $this->faker->boolean() ? date('Y-m-d H:i:s', strtotime($opened) + $this->faker->numberBetween(60, 3600)) : null,
            'replied_at' => $opened && $this->faker->boolean() ? date('Y-m-d H:i:s', strtotime($opened) + $this->faker->numberBetween(3600, 86400)) : null,
            'created_at' => date('Y-m-d H:i:s', strtotime('-'.$this->faker->numberBetween(0, 30).' days'))
        ];
    }
}