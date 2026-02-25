<?php

namespace Database\Factories;

use App\Enums\EducationLevelEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'level' => EducationLevelEnum::COLLEGE,
            'title' => $this->faker->company(),
            'description' => $this->faker->randomElement([
                'Bachelor of Science in Nursing',
                'Caregiving NC II',
                'Housekeeping NC II',
                'High school graduate',
                'TESDA Household Services passer',
            ]),
            'from_date' => $this->faker->date(),
            'to_date' => $this->faker->date(),
        ];
    }
}
