<?php

namespace Database\Factories;

use App\Enums\CountryEnum;
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
            'description' => $this->faker->paragraph(30),
            'country' => CountryEnum::KSA,
        ];
    }
}
