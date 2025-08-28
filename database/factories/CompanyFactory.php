<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name'        => $this->faker->company,
            'business_type'       => $this->faker->randomElement(['IT', 'Construction', 'Healthcare', 'Retail', 'Finance']),
            'company_email'       => $this->faker->unique()->companyEmail,
            'company_address'     => $this->faker->address,
            'country'             => $this->faker->country,
            'company_description' => $this->faker->paragraph,
            'price_guidelines'    => $this->faker->sentence,
            'bussiness_knowledge' => $this->faker->text(100),
        ];
    }
}
