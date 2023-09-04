<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Concern;
use App\Models\ConcernReport;
use App\Models\Deployment;
use App\Models\ForeignAgency;
use App\Models\Handler;
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
                Handler::factory(10)->create(['agency_id' => $agency->id]);
                ForeignAgency::factory(10)->create(['agency_id' => $agency->id]);
                Worker::factory(100)->create(['agency_id' => $agency->id]);
                Concern::factory(10)->has(ConcernReport::factory(5))->create(['agency_id' => $agency->id]);

                foreach(Worker::query()->inRandomOrder()->limit(50)->get() as $worker)
                {
                    Deployment::factory()->create([
                        'agency_id' => $agency->id,
                        'worker_id' => $worker->id,
                        'foreign_agency_id' => ForeignAgency::query()->inRandomOrder()->first()->id,
                        'handler_id' => Handler::query()->inRandomOrder()->first()->id,
                    ]);
                }
            }
        }
    }
}
