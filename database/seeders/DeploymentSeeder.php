<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Deployment;
use App\Models\ForeignAgency;
use App\Models\Handler;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DeploymentSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing data or create if needed
        $agency = Agency::first();
        $foreignAgency = ForeignAgency::first();
        $handler = Handler::first();

        if (! $agency || ! $foreignAgency || ! $handler) {
            $this->command->warn('Required related models (Agency, ForeignAgency, Handler) not found. Please seed them first.');

            return;
        }

        // Get workers that don't have an active deployment yet
        $workersWithoutDeployment = Worker::query()
            ->whereDoesntHave('deployments', function ($query) {
                $query->where('status', 'DEPLOYED');
            })
            ->limit(5)
            ->get();

        if ($workersWithoutDeployment->isEmpty()) {
            $this->command->info('No workers found without active deployments. Creating deployments for existing workers...');

            // Fallback: get any workers
            $workersWithoutDeployment = Worker::query()
                ->limit(5)
                ->get();
        }

        $deploymentDate = Carbon::now()->subMonths(2);
        $endOfContractDate = $deploymentDate->copy()->addYears(2);

        $countries = ['Saudi Arabia', 'United Arab Emirates', 'Qatar', 'Kuwait', 'Bahrain', 'Oman'];
        $positions = ['Domestic Helper', 'Nurse', 'Caregiver', 'Driver', 'Engineer', 'Technician'];

        foreach ($workersWithoutDeployment as $index => $worker) {
            Deployment::create([
                'worker_id' => $worker->id,
                'agency_id' => $agency->id,
                'foreign_agency_id' => $foreignAgency->id,
                'handler_id' => $handler->id,
                'position' => $positions[$index % count($positions)],
                'address' => 'Block '.($index + 1).', Building '.chr(65 + $index).', Street '.($index + 10),
                'country' => $countries[$index % count($countries)],
                'date_deployed' => $deploymentDate,
                'end_of_contract_date' => $endOfContractDate,
                'has_left_country' => true,
                'flight_number' => 'FL'.str_pad((string) ($index + 100), 3, '0', STR_PAD_LEFT),
                'flight_date' => $deploymentDate->copy()->subDays(1),
                'airline' => ['Philippine Airlines', 'Emirates', 'Qatar Airways', 'Etihad'][$index % 4],
                'status' => 'DEPLOYED',
                'identification_no' => 'ID-'.strtoupper(uniqid()),
            ]);
        }

        $this->command->info('Created '.count($workersWithoutDeployment).' deployments dated 2 months ago ('.$deploymentDate->format('Y-m-d').').');
    }
}