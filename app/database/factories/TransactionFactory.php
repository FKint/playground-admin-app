<?php

namespace Database\Factories;

use App\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount_paid' => $this->faker->numberBetween(-1000, 1000) / 100,
            'amount_expected' => $this->faker->numberBetween(-1000, 1000) / 100,
            'remarks' => $this->faker->text(50),
        ];
    }
}
