<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition()
    {
        return [
            'first_name'    => $this->faker->firstName,
            'last_name'     => $this->faker->lastName,
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'policy_number' => 'POL-' . $this->faker->unique()->numerify('#####'),
            'address'       => $this->faker->address,
            'is_employee'   => $this->faker->boolean(20),
            'ssn'           => $this->faker->unique()->regexify('[0-9]{3}-[0-9]{2}-[0-9]{4}'),
        ];
    }
}
