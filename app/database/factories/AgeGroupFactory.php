<?php

namespace Database\Factories;

use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgeGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AgeGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->firstName,
            'abbreviation' => $this->faker->lastName,
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
        ];
    }
}
