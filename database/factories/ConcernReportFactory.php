<?php

namespace Database\Factories;

use App\Enums\ConcernStatusEnum;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConcernReport>
 */
class ConcernReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'worker_id' => Worker::inRandomOrder()->first()->id,
            'feedback' => $this->faker->randomElement([
                'Agency made follow-up. Employer agreed to release salary by end of month.',
                'Worker advised to submit written complaint. Case logged for POEA.',
                'Contract copy sent to worker. Awaiting signed acknowledgment.',
                'Transfer request under review. Foreign agency to confirm vacancy.',
                'No response from employer after 3 attempts. Escalated to embassy.',
            ]),
            'status' => $this->faker->randomElement(collect(ConcernStatusEnum::cases())->pluck('value')),
        ];
    }
}
