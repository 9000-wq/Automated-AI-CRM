<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ContactRole;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactRole>
 */
class ContactRoleFactory extends Factory
{
   protected $model = ContactRole::class;

    public function definition()
    {
        return [
            'label' => $this->faker->unique()->jobTitle, // or use word/sentence if needed
        ];
    }
}
