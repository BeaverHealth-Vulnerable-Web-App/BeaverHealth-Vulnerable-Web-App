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
            'first_name'     => $this->faker->firstName,
            'last_name'      => $this->faker->lastName,
            'date_of_birth'  => $this->faker->date('Y-m-d', '2002-12-31'),
            'policy_number'  => strtoupper($this->faker->unique()->bothify('POL###??')),
            'address'        => $this->faker->address,
            'is_employee'    => $this->faker->boolean(20), // 20% chance of being true
            'ssn'            => $this->faker->unique()->ssn,
        ];
    }
}
