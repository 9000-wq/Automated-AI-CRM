<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Plan;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
   
    protected $model = Plan::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 500), // price between 5 and 500
            'billing_cycle' => $this->faker->randomElement(['monthly', 'yearly']),
            'features' => json_encode([
                $this->faker->word(),
                $this->faker->word(),
                $this->faker->word(),
            ]),
        ];
    }

}
