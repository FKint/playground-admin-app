<?php

namespace Database\Factories;

use App\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

class TariffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tariff::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(100),
            'abbreviation' => $this->faker->text(20),
            'week_first_child' => $this->faker->numberBetween(1500, 5000) / 100,
            'week_later_children' => $this->faker->numberBetween(1500, 5000) / 100,
            'day_first_child' => $this->faker->numberBetween(200, 600) / 100,
            'day_later_children' => $this->faker->numberBetween(100, 300) / 100,
        ];
    }
}
