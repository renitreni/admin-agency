<?php

namespace Database\Seeders;

use App\Models\Deployment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonitoringSeeder extends Seeder
{
    public function run(): void
    {
        $deployedWorkers = Deployment::query()
            ->with(['worker.workerInformation'])
            ->where('status', 'DEPLOYED')
            ->orderBy('id')
            ->get()
            ->unique('worker_id')
            ->values();

        $workerOverMonth = $deployedWorkers->get(0)?->worker;
        $workerUnderMonth = $deployedWorkers->get(1)?->worker;

        if ($workerOverMonth) {
            DB::table('monitorings')->updateOrInsert(
                [
                    'agency_id' => $workerOverMonth->agency_id,
                    'worker_id' => $workerOverMonth->id,
                    'report' => 'Monthly welfare check: worker reports stable conditions and regular communication with family.',
                ],
                [
                    'passport_number' => (string) optional($workerOverMonth->workerInformation)->passport_number,
                    'secret_code' => (string) $workerOverMonth->code,
                    'latitude' => 24.7136000,
                    'longitude' => 46.6753000,
                    'updated_at' => Carbon::now()->subDays(45),
                    'created_at' => Carbon::now()->subDays(45),
                ]
            );
        }

        if ($workerUnderMonth) {
            DB::table('monitorings')->updateOrInsert(
                [
                    'agency_id' => $workerUnderMonth->agency_id,
                    'worker_id' => $workerUnderMonth->id,
                    'report' => 'Recent monitoring update: worker is adjusting well and no urgent concerns were raised.',
                ],
                [
                    'passport_number' => (string) optional($workerUnderMonth->workerInformation)->passport_number,
                    'secret_code' => (string) $workerUnderMonth->code,
                    'latitude' => 25.2048000,
                    'longitude' => 55.2708000,
                    'updated_at' => Carbon::now()->subDays(10),
                    'created_at' => Carbon::now()->subDays(10),
                ]
            );
        }
    }
}

