<?php

namespace Database\Factories;

use App\Models\WeekDay;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeekDayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WeekDay::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'days_offset' => $this->faker->numberBetween(0, 6),
            'name' => '',
        ];
    }
}
