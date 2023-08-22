<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Agency;
use App\Models\Applicant;
use App\Models\Concern;
use App\Models\ConcernReport;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local'])) {
            User::factory()->create([
                'name' => 'admin',
                'email' => 'admin@example.com',
            ]);

            $agencies = Agency::factory(10)->create();

            Applicant::factory(10)->create();

            foreach ($agencies as $agency) {
                Worker::factory(10)->create(['agency_id' => $agency->id]);
                Concern::factory(10)->has(ConcernReport::factory(5))->create(['agency_id' => $agency->id]);
            }
        }
    }
}
