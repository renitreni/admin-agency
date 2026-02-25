<?php

namespace Database\Factories;

use App\Enums\ConcernStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Concern>
 */
class ConcernFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement([
                'Delay in contract processing',
                'Employer not responding to worker concerns',
                'Salary remittance issue',
                'Request for transfer of employer',
                'Documentation follow-up',
            ]),
            'description' => $this->faker->randomElement([
                'Worker reported delayed release of contract. Following up with foreign agency.',
                'Employer has not replied to repeated contact. Escalating to POEA.',
                'Worker claims salary not remitted on schedule. Verifying with employer.',
                'Worker requesting transfer due to change in household setup.',
                'Pending OEC renewal. Waiting for employer confirmation letter.',
            ]),
            'status' => $this->faker->randomElement(collect(ConcernStatusEnum::cases())->pluck('value')),
        ];
    }
}
