<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Concern;
use App\Models\ConcernReport;
use App\Models\Deployment;
use App\Models\ForeignAgency;
use App\Models\Handler;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Worker;
use App\Models\WorkerInformation;
use Database\Factories\WorkerFactory;
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
                'email' => 'yaramayservices@gmail.com',
            ]);

            foreach ($agencies as $agency) {
                Handler::factory(10)->create(['agency_id' => $agency->id]);
                $foreignAgency = ForeignAgency::factory(10)->create(['agency_id' => $agency->id]);
                $workers = Worker::factory(100)->create(['agency_id' => $agency->id]);
                Concern::factory(10)->has(ConcernReport::factory(5))->create(['agency_id' => $agency->id]);

                foreach ($workers as $worker) {
                    Voucher::factory()->create([
                        'agency_id' => $agency->id,
                        'worker_id' => $worker->id,
                        'foreign_agency_id' => $foreignAgency->random()->id,
                    ]);
                }
                
                $filteredWorker = Worker::query()
                    ->where(['agency_id' => $agency->id])
                    ->inRandomOrder()
                    ->limit(50)
                    ->get();

                foreach ($filteredWorker as $worker) {
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
