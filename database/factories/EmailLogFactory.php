<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmailLog;
use App\Models\Lead;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailLog>
 */
class EmailLogFactory extends Factory
{
    
    protected $model = EmailLog::class;

    public function definition()
    {
        return [
            'email'     => $this->faker->unique()->safeEmail,
            'cc'        => $this->faker->optional()->safeEmail,
            'lead_id' => Lead::factory(), // assuming parent_id refers to a Lead
            'subject'   => $this->faker->sentence,
            'body'      => $this->faker->paragraphs(3, true),
        ];
    }

}
