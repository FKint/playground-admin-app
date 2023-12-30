<?php

namespace Database\Factories;

use App\Models\Supplement;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(50),
            'price' => $this->faker->numberBetween(10, 1000) / 100,
        ];
    }
}
