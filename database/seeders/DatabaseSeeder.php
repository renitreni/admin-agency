<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Agency;
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
            Worker::factory(10)->create();
            Concern::factory(10)->has(ConcernReport::factory(5))->create();
            Agency::factory(10)->create();
        }
    }
}
