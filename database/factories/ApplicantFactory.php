<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant>
 */
class ApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'contact_number' => $this->faker->phoneNumber,
            'date_hired' => $this->faker->date,
            'address' => $this->faker->address,
            'date_birth' => $this->faker->date,
            'place_birth' => $this->faker->city,
            'passport_number' => $this->faker->numerify('######'),
            'passport_place_issue' => $this->faker->city,
            'passport_date_issue' => $this->faker->date,
            'passport_date_expired' => $this->faker->date,
            'elementary' => $this->faker->sentence,
            'high_school' => $this->faker->sentence,
            'vocational' => $this->faker->sentence,
            'college' => $this->faker->sentence,
            'father_name' => $this->faker->name('male'),
            'father_occupation' => $this->faker->jobTitle,
            'mother_name' => $this->faker->name('female'),
            'mother_occupation' => $this->faker->jobTitle,
            'spouse_name' => $this->faker->name,
            'spouse_occupation' => $this->faker->jobTitle,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'religion' => $this->faker->word,
            'civil_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'height' => $this->faker->randomFloat(2, 150, 200),
            'weight' => $this->faker->randomFloat(2, 50, 150),
            'objectives' => $this->faker->paragraph,
            'pic_face' => 'default_face.jpg',
            'pic_body' => 'default_body.jpg',
        ];
    }
}
