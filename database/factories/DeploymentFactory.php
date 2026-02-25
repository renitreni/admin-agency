<?php

namespace Database\Factories;

use App\Enums\PositionEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deployment>
 */
class DeploymentFactory extends Factory
{
    private static array $addresses = [
        'Block 12, Al Nahda District, Riyadh',
        'Villa 45, Al Barsha, Dubai',
        'Flat 203, Salmiya, Kuwait City',
        'House 7, Industrial Area, Dammam',
        'Unit 15, Jeddah Corniche Residences',
    ];

    private static array $airlines = [
        'Philippine Airlines',
        'Cebu Pacific',
        'Saudia',
        'Emirates',
        'Gulf Air',
        'Kuwait Airways',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateDeployed = $this->faker->dateTimeBetween('-2 years', '-3 months');
        $endOfContract = (clone $dateDeployed)->modify('+2 years');
        $hasLeft = $this->faker->boolean(20);
        $flightDate = $hasLeft ? $this->faker->dateTimeBetween($dateDeployed, 'now') : null;

        return [
            'position' => PositionEnum::DH,
            'country' => $this->faker->randomElement(['KSA', 'UAE', 'Kuwait', 'Qatar', 'Bahrain']),
            'address' => $this->faker->randomElement(self::$addresses),
            'date_deployed' => $dateDeployed,
            'end_of_contract_date' => $endOfContract,
            'has_left_country' => $hasLeft,
            'flight_number' => $flightDate ? $this->faker->regexify('[A-Z]{2}[0-9]{3,4}') : null,
            'flight_date' => $flightDate,
            'airline' => $flightDate ? $this->faker->randomElement(self::$airlines) : null,
            'status' => $hasLeft ? 'REPATRIATED' : 'DEPLOYED',
            'identification_no' => 'DEP-' . $this->faker->numerify('########'),
        ];
    }
}
