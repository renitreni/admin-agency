<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewAgenciesSeeder extends Seeder
{
    /**
     * Seed the 3 new agencies with 2 accounts each.
     * Data from license documents for sys.yaramay.online
     */
    public function run(): void
    {
        $agenciesData = [
            [
                'name' => "Mother's Way Overseas Manpower Specialist Corp.",
                'email' => 'admin1@mothersway.com',
                'license_no' => 'POEA-193-LB-120922-R',
                'address' => 'Ground Floor, Victoria Casa Realty and Leasing Corp. Building, 1055 Remedios Street cor. F.T. Benitez Street, Barangay 689, Zone 75, Malate, Manila',
                'users' => [
                    ['name' => "Admin 1 - Mother's Way", 'email' => 'admin1@mothersway.com'],
                    ['name' => "Admin 2 - Mother's Way", 'email' => 'admin2@mothersway.com'],
                ],
            ],
            [
                'name' => 'Meiden Way Management Specialist Inc.',
                'email' => 'admin1@meidenway.com',
                'license_no' => 'DMW-103-LB-05312023-R',
                'address' => 'Mezzanine Floor, Victoria Casa Ciudad Building, 618 Gamban St., Pasay City',
                'users' => [
                    ['name' => 'Admin 1 - Meiden Way', 'email' => 'admin1@meidenway.com'],
                    ['name' => 'Admin 2 - Meiden Way', 'email' => 'admin2@meidenway.com'],
                ],
            ],
            [
                'name' => 'TG International Artist and Manpower Exponent Inc.',
                'email' => 'admin1@tginternational.com',
                'license_no' => 'DMW-813-LB-09012026-R',
                'address' => 'Second Floor, Victoria Casa Ciudad Building, 618 Gamban Street, Pasay City',
                'users' => [
                    ['name' => 'Admin 1 - TG International', 'email' => 'admin1@tginternational.com'],
                    ['name' => 'Admin 2 - TG International', 'email' => 'admin2@tginternational.com'],
                ],
            ],
        ];

        $generatedPasswords = [];

        DB::transaction(function () use ($agenciesData, &$generatedPasswords) {
            foreach ($agenciesData as $agencyData) {
                $usersData = $agencyData['users'];
                unset($agencyData['users']);

                // Create agency
                $agency = Agency::create([
                    'name' => $agencyData['name'],
                    'email' => $agencyData['email'],
                ]);

                $this->command->info("Created agency: {$agency->name} (UUID: {$agency->uuid})");

                // Create 2 users for this agency
                foreach ($usersData as $userData) {
                    $password = Str::random(16);
                    
                    $user = User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => Hash::make($password),
                        'user_type' => User::TYPE_AGENCY,
                        'email_verified_at' => now(),
                    ]);

                    // Link user to agency via pivot table
                    DB::table('agency_user')->insert([
                        'agency_id' => $agency->id,
                        'user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $generatedPasswords[] = [
                        'agency' => $agency->name,
                        'user' => $user->name,
                        'email' => $user->email,
                        'password' => $password,
                    ];

                    $this->command->info("  Created user: {$user->name} ({$user->email})");
                }
            }
        });

        // Output generated passwords
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('GENERATED PASSWORDS (save these now!)');
        $this->command->info('========================================');
        foreach ($generatedPasswords as $cred) {
            $this->command->line("Agency: {$cred['agency']}");
            $this->command->line("  User: {$cred['user']}");
            $this->command->line("  Email: {$cred['email']}");
            $this->command->line("  Password: {$cred['password']}");
            $this->command->newLine();
        }
        $this->command->info('========================================');
    }
}