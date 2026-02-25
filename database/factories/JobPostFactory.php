<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPost>
 */
class JobPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'posted_by' => 1,
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->randomElement([
                'Minimum 2 years experience. TESDA certified. Must be willing to undergo medical and orientation.',
                'Household helper position. Employer provides accommodation. Contract period 2 years renewable.',
                'Caregiver for elderly. Nursing background preferred. Required documents: resume, passport, certificate.',
                'Factory worker. Overtime may be required. Agency handles visa and ticket processing.',
                'Hotel staff. Good English communication. Benefits include accommodation and annual leave.',
            ]),
            'country' => Country::query()->inRandomOrder()->first()->country_name,
        ];
    }
}
