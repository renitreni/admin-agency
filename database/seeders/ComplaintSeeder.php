<?php

namespace Database\Seeders;

use App\Models\Complaint;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaints = [
            [
                'foreign_recruitment_agency' => 'Global Manpower Solutions Inc.',
                'ofw_full_name' => 'Maria Santos',
                'gender' => 'female',
                'birthdate' => '1985-03-15',
                'occupation' => 'Domestic Helper',
                'nation_id' => 'NID-001234',
                'passport_no' => 'P1234567',
                'email' => 'maria.santos@example.com',
                'contact_person' => 'Juan Santos',
                'primary_contact' => '+639171234567',
                'secondary_contact' => '+639281234567',
                'address_abroad' => 'Flat 12, Building 5, Dubai Marina, UAE',
                'latitude' => 25.0800,
                'longitude' => 55.1397,
                'complaint' => 'Withheld salary for three months. Agency did not provide contract copy upon deployment.',
            ],
            [
                'foreign_recruitment_agency' => 'Pacific Overseas Employment',
                'ofw_full_name' => 'Roberto Cruz',
                'gender' => 'male',
                'birthdate' => '1990-07-22',
                'occupation' => 'Construction Worker',
                'nation_id' => 'NID-002345',
                'passport_no' => 'P2345678',
                'email' => 'roberto.cruz@example.com',
                'contact_person' => 'Ana Cruz',
                'primary_contact' => '+639189876543',
                'secondary_contact' => null,
                'address_abroad' => 'Riyadh Industrial Area, Saudi Arabia',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'complaint' => 'Overcharging of placement fees. Working conditions differ from contract.',
            ],
            [
                'foreign_recruitment_agency' => 'Asia Pacific Recruitment Co.',
                'ofw_full_name' => 'Lorna Dela Cruz',
                'gender' => 'female',
                'birthdate' => '1988-11-08',
                'occupation' => 'Caregiver',
                'nation_id' => 'NID-003456',
                'passport_no' => 'P3456789',
                'email' => 'lorna.delacruz@example.com',
                'contact_person' => 'Pedro Dela Cruz',
                'primary_contact' => '+639201112233',
                'secondary_contact' => '+639224445566',
                'address_abroad' => 'Toronto, Ontario, Canada',
                'latitude' => 43.6532,
                'longitude' => -79.3832,
                'complaint' => 'Agency failed to process OEC renewal on time. No support when employer changed terms.',
            ],
            [
                'foreign_recruitment_agency' => 'Metro International Agency',
                'ofw_full_name' => 'Jose Ramirez',
                'gender' => 'male',
                'birthdate' => '1982-01-30',
                'occupation' => 'Seafarer',
                'nation_id' => 'NID-004567',
                'passport_no' => 'P4567890',
                'email' => 'jose.ramirez@example.com',
                'contact_person' => 'Carmen Ramirez',
                'primary_contact' => '+639237778899',
                'secondary_contact' => null,
                'address_abroad' => 'Vessel MV Ocean Star, Singapore registry',
                'latitude' => null,
                'longitude' => null,
                'complaint' => 'Unpaid overtime. Agency did not remit SSS and Pag-IBIG contributions.',
            ],
            [
                'foreign_recruitment_agency' => 'United Manpower Export',
                'ofw_full_name' => 'Elena Reyes',
                'gender' => 'female',
                'birthdate' => '1992-05-12',
                'occupation' => 'Hotel Staff',
                'nation_id' => 'NID-005678',
                'passport_no' => 'P5678901',
                'email' => 'elena.reyes@example.com',
                'contact_person' => 'Miguel Reyes',
                'primary_contact' => '+639251234567',
                'secondary_contact' => '+639268901234',
                'address_abroad' => 'Kuwait City, Kuwait',
                'latitude' => 29.3759,
                'longitude' => 47.9774,
                'complaint' => 'Contract substitution. Salary lower than agreed. Requesting assistance for repatriation.',
            ],
        ];

        foreach ($complaints as $data) {
            Complaint::create($data);
        }
    }
}
