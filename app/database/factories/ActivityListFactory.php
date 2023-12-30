<?php

namespace Database\Factories;

use App\Models\ActivityList;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(100),
            'show_on_attendance_form' => $this->faker->boolean,
            'show_on_dashboard' => $this->faker->boolean,
        ];
    }
}
