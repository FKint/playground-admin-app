<?php

namespace Database\Factories;

use App\DayPart;
use Illuminate\Database\Eloquent\Factories\Factory;

class DayPartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DayPart::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'default' => false,
            'name' => 'Full Day',
            'order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
