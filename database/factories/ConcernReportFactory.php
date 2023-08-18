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
            'feedback' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(collect(ConcernStatusEnum::cases())->pluck('value')),
        ];
    }
}
