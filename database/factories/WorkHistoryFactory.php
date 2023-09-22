<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkHistory>
 */
class WorkHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'address' => $this->faker->company(),
            'from_date' => $this->faker->date(),
            'to_date' => $this->faker->date(),
            'position' => $this->faker->jobTitle(),
        ];
    }
}
