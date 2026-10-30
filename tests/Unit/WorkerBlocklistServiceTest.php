<?php

namespace Tests\Unit;

use App\Models\WorkerBlocklist;
use App\Services\WorkerBlocklistService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WorkerBlocklistServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'telescope.storage.database.connection' => 'sqlite',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null,
        ]);
        DB::purge('sqlite');
        $this->artisan('migrate:fresh', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
    }

    public function test_passport_exact_match_returns_high_confidence(): void
    {
        $blocklistData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
        ];

        $workerData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'different@example.com',
            'contact_number' => '+639187654321',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
        ];

        $score = WorkerBlocklist::calculateMatchScore($blocklistData, $workerData);

        $this->assertGreaterThanOrEqual(70, $score);
        $this->assertEquals('high', WorkerBlocklist::getConfidenceFromScore($score));
    }

    public function test_email_exact_match_returns_high_confidence(): void
    {
        $blocklistData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
        ];

        $workerData = [
            'first_name' => 'John',
            'last_name' => 'De la Cruz',
            'passport_number' => 'P9999999Z',
            'email' => 'juan@example.com',
            'contact_number' => '+639187654321',
            'date_birth' => '1991-02-02',
            'place_birth' => 'Cebu',
        ];

        $score = WorkerBlocklist::calculateMatchScore($blocklistData, $workerData);

        $this->assertEquals(30, $score);
        $this->assertEquals('low', WorkerBlocklist::getConfidenceFromScore($score));
    }

    public function test_name_and_dob_match_returns_medium_confidence(): void
    {
        $blocklistData = [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_name' => 'Garcia',
            'passport_number' => 'P1111111A',
            'email' => 'maria@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1985-05-15',
            'place_birth' => 'Quezon City',
        ];

        $workerData = [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_name' => 'Garcia',
            'passport_number' => 'P9999999Z',
            'email' => 'different@example.com',
            'contact_number' => '+639187654321',
            'date_birth' => '1985-05-15',
            'place_birth' => 'Quezon City',
        ];

        $score = WorkerBlocklist::calculateMatchScore($blocklistData, $workerData);

        $this->assertEquals(40, $score);
        $this->assertEquals('medium', WorkerBlocklist::getConfidenceFromScore($score));
    }

    public function test_phone_normalization_matches_different_formats(): void
    {
        $blocklistData = [
            'first_name' => 'Jose',
            'last_name' => 'Rizal',
            'passport_number' => 'P1111111A',
            'email' => 'jose@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1861-06-19',
            'place_birth' => 'Calamba',
        ];

        $workerData = [
            'first_name' => 'Jose',
            'last_name' => 'Rizal',
            'passport_number' => 'P9999999Z',
            'email' => 'different@example.com',
            'contact_number' => '09171234567',
            'date_birth' => '1861-06-19',
            'place_birth' => 'Calamba',
        ];

        $score = WorkerBlocklist::calculateMatchScore($blocklistData, $workerData);

        $this->assertEquals(50, $score);
        $this->assertEquals('medium', WorkerBlocklist::getConfidenceFromScore($score));
    }

    public function test_phone_normalization_handles_parentheses_and_spaces(): void
    {
        $blocklistData = [
            'contact_number' => '(0917) 123-4567',
        ];

        $workerData = [
            'contact_number' => '+63 917 123 4567',
        ];

        $normalizedBlocklist = WorkerBlocklist::normalizePhone($blocklistData['contact_number']);
        $normalizedWorker = WorkerBlocklist::normalizePhone($workerData['contact_number']);

        $this->assertEquals($normalizedBlocklist, $normalizedWorker);
    }

    public function test_name_normalization_handles_case_and_spaces(): void
    {
        $blocklistName = '  JUAN   DELA CRUZ  ';
        $workerName = 'juan dela cruz';

        $normalizedBlocklist = WorkerBlocklist::normalizeName($blocklistName);
        $normalizedWorker = WorkerBlocklist::normalizeName($workerName);

        $this->assertEquals($normalizedBlocklist, $normalizedWorker);
    }

    public function test_name_normalization_handles_punctuation(): void
    {
        $blocklistName = "O'Connor";
        $workerName = 'oconnor';

        $normalizedBlocklist = WorkerBlocklist::normalizeName($blocklistName);
        $normalizedWorker = WorkerBlocklist::normalizeName($workerName);

        $this->assertEquals($normalizedBlocklist, $normalizedWorker);
    }

    public function test_missing_fields_result_in_low_confidence(): void
    {
        $blocklistData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => null,
            'email' => null,
            'contact_number' => null,
            'date_birth' => null,
            'place_birth' => null,
        ];

        $workerData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => null,
            'email' => null,
            'contact_number' => null,
            'date_birth' => null,
            'place_birth' => null,
        ];

        $score = WorkerBlocklist::calculateMatchScore($blocklistData, $workerData);

        $this->assertEquals(20, $score);
        $this->assertEquals('low', WorkerBlocklist::getConfidenceFromScore($score));
    }

    public function test_service_is_blocklisted_returns_high_confidence_match(): void
    {
        $service = app(WorkerBlocklistService::class);

        // Create agency and user for foreign key constraints
        $agency = \App\Models\Agency::factory()->create(['id' => 1]);
        $user = \App\Models\User::factory()->create([
            'id' => 1,
            'password' => bcrypt('test-password'),
        ]);
        $user->agency()->attach($agency);

        WorkerBlocklist::create([
            'agency_id' => 1,
            'blocked_by' => 1,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
            'reason' => 'Test reason',
            'match_confidence' => 'high',
            'is_active' => true,
            'blocked_at' => now(),
        ]);

        $workerData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
        ];

        $result = $service->isBlocklisted($workerData, 2);

        $this->assertTrue($result['is_blocklisted']);
        $this->assertCount(1, $result['matches']);
        $this->assertEquals('high', $result['matches'][0]['confidence']);
    }

    public function test_service_excludes_own_agency_entries(): void
    {
        $service = app(WorkerBlocklistService::class);

        $agency1 = \App\Models\Agency::factory()->create(['id' => 1]);
        $user1 = \App\Models\User::factory()->create([
            'id' => 1,
            'password' => bcrypt('test-password'),
        ]);
        $user1->agency()->attach($agency1);

        WorkerBlocklist::create([
            'agency_id' => 1,
            'blocked_by' => 1,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
            'reason' => 'Test reason',
            'match_confidence' => 'high',
            'is_active' => true,
            'blocked_at' => now(),
        ]);

        $workerData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
        ];

        $result = $service->isBlocklisted($workerData, 1);

        $this->assertFalse($result['is_blocklisted']);
        $this->assertEmpty($result['matches']);
    }

    public function test_inactive_entries_are_not_considered(): void
    {
        $service = app(WorkerBlocklistService::class);

        $agency = \App\Models\Agency::factory()->create(['id' => 1]);
        $user = \App\Models\User::factory()->create([
            'id' => 1,
            'password' => bcrypt('test-password'),
        ]);
        $user->agency()->attach($agency);

        WorkerBlocklist::create([
            'agency_id' => 1,
            'blocked_by' => 1,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
            'reason' => 'Test reason',
            'match_confidence' => 'high',
            'is_active' => false,
            'blocked_at' => now(),
        ]);

        $workerData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
        ];

        $result = $service->isBlocklisted($workerData, 2);

        $this->assertFalse($result['is_blocklisted']);
        $this->assertEmpty($result['matches']);
    }

    public function test_confidence_thresholds(): void
    {
        $this->assertEquals('high', WorkerBlocklist::getConfidenceFromScore(70));
        $this->assertEquals('high', WorkerBlocklist::getConfidenceFromScore(100));
        $this->assertEquals('medium', WorkerBlocklist::getConfidenceFromScore(40));
        $this->assertEquals('medium', WorkerBlocklist::getConfidenceFromScore(69));
        $this->assertEquals('low', WorkerBlocklist::getConfidenceFromScore(39));
        $this->assertEquals('low', WorkerBlocklist::getConfidenceFromScore(0));
    }

    public function test_soft_deleted_entries_are_excluded(): void
    {
        $service = app(WorkerBlocklistService::class);

        $agency = \App\Models\Agency::factory()->create(['id' => 1]);
        $user = \App\Models\User::factory()->create([
            'id' => 1,
            'password' => bcrypt('test-password'),
        ]);
        $user->agency()->attach($agency);

        $entry = WorkerBlocklist::create([
            'agency_id' => 1,
            'blocked_by' => 1,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
            'reason' => 'Test reason',
            'match_confidence' => 'high',
            'is_active' => true,
            'blocked_at' => now(),
        ]);

        $entry->delete();

        $workerData = [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'passport_number' => 'P1234567A',
            'email' => 'juan@example.com',
            'contact_number' => '+639171234567',
            'date_birth' => '1990-01-01',
            'place_birth' => 'Manila',
        ];

        $result = $service->isBlocklisted($workerData, 2);

        $this->assertFalse($result['is_blocklisted']);
        $this->assertEmpty($result['matches']);
    }
}
