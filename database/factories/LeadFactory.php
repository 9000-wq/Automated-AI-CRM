<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lead;
use App\Models\Account;
use App\Models\User;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{

    protected $model = Lead::class;

    public function definition()
    {
        // First, create related models or use existing ones
        $company = Company::factory()->create();
        $account = Account::factory()->for($company)->create();
        $user = User::factory()->create();

        return [
            'case_ref'    => strtoupper($this->faker->bothify('CASE-####')),
            'name'        => $this->faker->name(),
            'source'      => $this->faker->randomElement(['website', 'email', 'referral', 'phone']),
            'status'      => $this->faker->randomElement(['new', 'contacted', 'qualified', 'lost']),
            'assigned_to' => $user->id,
            'company_id'  => $company->id,
            'account_id'  => $account->id,
        ];
    }

}
