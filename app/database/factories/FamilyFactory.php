<?php

namespace Database\Factories;

use App\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

class FamilyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Family::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'guardian_first_name' => $this->faker->firstName,
            'guardian_last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
