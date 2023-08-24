<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Concern;
use App\Models\ConcernReport;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerInformation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local'])) {
            $agencies = Agency::factory(10)->has(Worker::factory(10)->has(WorkerInformation::factory()))->create();

            User::factory()->has(Agency::factory())->create([
                'name' => 'admin',
                'email' => 'admin@example.com',
            ]);

            foreach ($agencies as $agency) {
                Worker::factory(10)->create(['agency_id' => $agency->id]);
                Concern::factory(10)->has(ConcernReport::factory(5))->create(['agency_id' => $agency->id]);
            }
        }
    }
}
