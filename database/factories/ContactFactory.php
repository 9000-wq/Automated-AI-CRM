<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use App\Models\Account;
use App\Models\ContactRole;
use App\Models\Company;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        // return [
        //     'name'            => $this->faker->name,
        //     'email'           => $this->faker->unique()->safeEmail,
        //     'phone'           => $this->faker->numerify('##########'), // adjust digits if needed
        //     'birthday'        => $this->faker->optional()->date(),
        //     'address'         => $this->faker->optional()->address,
        //     'description'     => $this->faker->optional()->sentence,
        //     'lead_id'         => null, // you can override in tests if needed
        //     'contact_role_id' => ContactRole::factory(),
        //     'account_id'      => Account::factory(),
        //     'company_id'      => Company::factory(),
        // ];
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'birthday' => $this->faker->date(),
            'address' => $this->faker->address,
            'description' => $this->faker->sentence,
            'lead_id' => null, // optional
            // ✅ actually create a role so FK is valid
            'contact_role_id' => ContactRole::factory(),
            'account_id' => null,
            'company_id' => 1,
        ];
    }
}
