<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->randomElement([
                'Housekeeping and laundry',
                'Cooking and meal preparation',
                'Elderly care and basic nursing',
                'Child care and tutoring',
                'Driving and vehicle maintenance',
            ]),
        ];
    }
}
