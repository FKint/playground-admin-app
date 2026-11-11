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
            // TODO: random flags make every test that renders a list non-deterministic (the list may or may not show on the
            // attendance form/dashboard). Prefer deterministic defaults plus explicit states, and set the flags in the tests.
            'show_on_attendance_form' => $this->faker->boolean,
            'show_on_dashboard' => $this->faker->boolean,
        ];
    }
}
