<?php

namespace Database\Factories;

use App\Models\Year;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class YearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Year::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->text(20),
            // 'organization_id' => Organization::factory()->create()->id,
            'title' => '2018',
            'invoice_header_image' => null,
            'invoice_header_text' => 'VZW\nThe Address\nThe Town',
            'invoice_bank_account' => 'DE12 3421 3243 5988 2343',
        ];
    }
}
