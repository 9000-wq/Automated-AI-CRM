<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Call;
use App\Models\Lead;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Call>
 */
class CallFactory extends Factory
{
    
    protected $model = Call::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement(['planned', 'held', 'canceled']),
            'direction' => $this->faker->randomElement(['inbound', 'outbound']),
            'date_start' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'date_end' => $this->faker->dateTimeBetween('now', '+1 month'),
            'duration' => $this->faker->numberBetween(1, 120), // in minutes
            'parent_type' => $this->faker->randomElement(['Lead', 'Contact', 'Opportunity']),
            'parent_name' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'assigned_user_name' => $this->faker->name,
            'teams' => json_encode([$this->faker->word, $this->faker->word]), // array as JSON
            'users' => json_encode([$this->faker->name, $this->faker->name]),
            'contacts' => json_encode([$this->faker->name, $this->faker->phoneNumber]),
            'leads' => json_encode([$this->faker->company, $this->faker->email]),
            'lead_id' => Lead::factory(), // relation
            'company_id' => User::factory()->create()->company_id, // assuming User has company_id
            'transcript' => $this->faker->text(200),
            'sentiment' => $this->faker->randomElement(['positive', 'neutral', 'negative']),
            'outcome' => $this->faker->sentence(5),
            'audio_link' => $this->faker->url,
        ];
    }
}
