<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
   
    protected $model = Account::class;

    public function definition()
    {
        return [
            'name'       => $this->faker->company,
            'industry'   => $this->faker->word,
            'email'      => $this->faker->unique()->companyEmail,
            'phone'      => $this->faker->numerify('##########'), // 10-digit number
            'website'    => $this->faker->url,
            'address'    => $this->faker->address,
            'city'       => $this->faker->city,
            'country'    => $this->faker->country,
            'status'     => $this->faker->randomElement(['active', 'inactive']),
            'company_id' => \App\Models\Company::factory(), // assumes a company exists
        ];
    }


}
