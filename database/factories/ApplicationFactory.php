<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->lastName(),
            'contact_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'cover_letter' => $this->faker->randomElement([
                'I have 3 years experience as household helper. I am TESDA certified and ready for deployment.',
                'Willing to work abroad. I completed caregiving training and have references from previous employer.',
                'Seeking opportunity to work as DH. I can cook, clean, and care for children. Available immediately.',
            ]),
            'accepted_terms_and_condition' => true,
        ];
    }
}
