<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Application;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Tests\TestCase;

class CvSubmissionTest extends TestCase
{
    private Agency $agency;

    private array $uploadPaths = [];

    private function uploadPath(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'cv-test-');
        file_put_contents($path, $content);
        $this->uploadPaths[] = $path;

        return $path;
    }

    protected function tearDown(): void
    {
        foreach ($this->uploadPaths as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Never run these migrations against a developer or production database.
        config(['telescope.storage.database.connection' => 'sqlite', 'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
        DB::purge('sqlite');
        $this->artisan('migrate:fresh', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
        Storage::fake('private_cv', ['throw' => true]);
        Storage::fake('public');
        $this->agency = Agency::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'first_name' => 'Juan', 'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com', 'contact_number' => '+639171234567',
            'accepted_terms_and_condition' => '1',
            'resume' => new UploadedFile($this->uploadPath("%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF"), 'resume.pdf', null, null, true),
        ], $overrides);
    }

    private function endpoint(?JobPost $job = null): string
    {
        return '/api/v1/application/'.$this->agency->uuid.($job ? '/job/'.$job->uuid : '');
    }

    private function job(array $overrides = []): JobPost
    {
        return JobPost::create(array_replace([
            'agency_id' => $this->agency->id, 'uuid' => (string) Str::uuid(),
            'title' => 'Electrician', 'posted_by' => 'Recruiter',
            'description' => '<p>Electrical installation</p>', 'country' => 'Saudi Arabia', 'is_published' => 1,
        ], $overrides));
    }

    public function test_general_cv_is_private_and_response_contains_no_personal_data(): void
    {
        $response = $this->postJson($this->endpoint(), $this->payload())->assertCreated();
        $this->assertSame(['message', 'application_id'], array_keys($response->json()));
        $application = Application::findOrFail($response->json('application_id'));
        $this->assertNull($application->job_post_id);
        $this->assertNull($application->middle_name);
        $this->assertNull($application->cover_letter);
        $media = $application->getFirstMedia('resumes');
        $this->assertSame('private_cv', $media->disk);
        Storage::disk('private_cv')->assertExists($media->getPathRelativeToRoot());
        $this->assertCount(1, Storage::disk('private_cv')->allFiles());
        $this->assertCount(0, Storage::disk('public')->allFiles());
        $this->get('/storage/'.$media->getPathRelativeToRoot())->assertNotFound();
    }

    public function test_specific_job_can_be_selected_through_either_endpoint(): void
    {
        $job = $this->job();
        $this->postJson($this->endpoint(), $this->payload(['job_id' => $job->uuid]))->assertCreated();
        $this->postJson($this->endpoint($job), $this->payload())->assertCreated();
        $this->assertSame(2, Application::where('job_post_id', $job->id)->count());
    }

    public function test_optional_details_are_saved(): void
    {
        $this->postJson($this->endpoint(), $this->payload(['middle_name' => 'Santos', 'cover_letter' => 'Experienced electrician.']))->assertCreated();
        $this->assertDatabaseHas('applications', ['middle_name' => 'Santos', 'cover_letter' => 'Experienced electrician.']);
    }

    public static function invalidFields(): array
    {
        return [
            'consent missing' => ['accepted_terms_and_condition', null],
            'consent false' => ['accepted_terms_and_condition', '0'],
            'name missing' => ['first_name', ''],
            'email invalid' => ['email', 'invalid'],
            'phone missing' => ['contact_number', ''],
            'job uuid invalid' => ['job_id', 'not-a-uuid'],
            'introduction too long' => ['cover_letter', str_repeat('a', 301)],
        ];
    }

    #[DataProvider('invalidFields')]
    public function test_invalid_fields_are_rejected(string $field, mixed $value): void
    {
        $this->postJson($this->endpoint(), $this->payload([$field => $value]))->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('applications', 0);
    }

    public function test_disguised_and_oversized_uploads_are_rejected(): void
    {
        $this->postJson($this->endpoint(), $this->payload(['resume' => new UploadedFile($this->uploadPath('<?php echo "hello";'), 'cv.pdf', null, null, true)]))
            ->assertUnprocessable()->assertJsonValidationErrors('resume');
        $this->postJson($this->endpoint(), $this->payload(['resume' => UploadedFile::fake()->create('cv.pdf', 5121, 'application/pdf')]))
            ->assertUnprocessable()->assertJsonValidationErrors('resume');
        $this->postJson($this->endpoint(), $this->payload(['resume' => UploadedFile::fake()->create('cv.exe', 10, 'application/pdf')]))
            ->assertUnprocessable()->assertJsonValidationErrors('resume');
        $this->assertDatabaseCount('applications', 0);
    }

    public function test_file_at_the_five_megabyte_boundary_is_accepted(): void
    {
        $this->postJson($this->endpoint(), $this->payload(['resume' => UploadedFile::fake()->create('cv.pdf', 5120, 'application/pdf')]))->assertCreated();
    }

    public function test_general_route_rejects_unpublished_deleted_and_unknown_jobs(): void
    {
        $unpublished = $this->job(['is_published' => 0]);
        $deleted = $this->job();
        $deleted->delete();
        foreach ([$unpublished->uuid, $deleted->uuid, (string) Str::uuid()] as $uuid) {
            $this->postJson($this->endpoint(), $this->payload(['job_id' => $uuid]))->assertUnprocessable()->assertJsonValidationErrors('job_id');
        }
        $this->assertDatabaseCount('applications', 0);
    }

    public function test_word_formats_are_accepted(): void
    {
        foreach (['doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'] as $extension => $mime) {
            $this->postJson($this->endpoint(), $this->payload(['resume' => UploadedFile::fake()->create('cv.'.$extension, 10, $mime)]))->assertCreated();
        }
    }

    public function test_unpublished_and_foreign_jobs_are_rejected(): void
    {
        $unpublished = $this->job(['is_published' => 0]);
        $foreign = $this->job(['agency_id' => Agency::factory()->create()->id]);
        foreach ([$unpublished, $foreign] as $job) {
            $this->postJson($this->endpoint($job), $this->payload())->assertUnprocessable()->assertJsonValidationErrors('job_id');
        }
        $this->postJson($this->endpoint(), $this->payload(['job_id' => $foreign->uuid]))->assertUnprocessable()->assertJsonValidationErrors('job_id');
    }

    public function test_deleted_missing_and_conflicting_jobs(): void
    {
        $job = $this->job();
        $this->postJson($this->endpoint($job), $this->payload(['job_id' => (string) Str::uuid()]))->assertUnprocessable()->assertJsonValidationErrors('job_id');
        $job->delete();
        $this->postJson($this->endpoint($job), $this->payload())->assertNotFound();
        $this->postJson('/api/v1/application/'.Str::uuid(), $this->payload())->assertNotFound();
    }

    public function test_submission_rate_limit(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson($this->endpoint(), $this->payload())->assertCreated();
        }
        $this->postJson($this->endpoint(), $this->payload())->assertStatus(429);
        $this->assertDatabaseCount('applications', 3);
    }

    public function test_partial_media_failure_rolls_back_application_and_cleans_files(): void
    {
        Event::listen(MediaHasBeenAddedEvent::class, fn () => throw new \RuntimeException('Simulated media failure'));
        $this->postJson($this->endpoint(), $this->payload())->assertServerError();
        $this->assertDatabaseCount('applications', 0);
        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('private_cv')->allFiles());
    }

    public function test_database_failure_cleans_staged_upload(): void
    {
        Application::creating(fn () => throw new \RuntimeException('Simulated database failure'));
        try {
            $this->postJson($this->endpoint(), $this->payload())->assertServerError();
            $this->assertDatabaseCount('applications', 0);
            $this->assertCount(0, Storage::disk('private_cv')->allFiles());
        } finally {
            Application::flushEventListeners();
        }
    }

    public function test_resume_download_is_restricted_to_agency_recruiters(): void
    {
        $response = $this->postJson($this->endpoint(), $this->payload())->assertCreated();
        $application = Application::findOrFail($response->json('application_id'));
        $url = route('applications.resume.download', ['agency' => $this->agency, 'application' => $application]);
        $this->getJson($url)->assertUnauthorized();
        $this->get($url)->assertRedirect(route('filament.admin.auth.login'));
        $user = User::factory()->create(['password' => 'test-password']);
        $this->actingAs($user)->get($url)->assertForbidden();
        $user->agency()->attach($this->agency);
        $this->get($url)->assertOk()->assertDownload('cv.pdf');
        $user->update(['user_type' => User::TYPE_FRA]);
        $this->get($url)->assertForbidden();
    }

    public function test_download_cannot_read_another_agencys_application(): void
    {
        $response = $this->postJson($this->endpoint(), $this->payload())->assertCreated();
        $otherAgency = Agency::factory()->create();
        $user = User::factory()->create(['password' => 'test-password']);
        $user->agency()->attach($otherAgency);
        $this->actingAs($user)->get(route('applications.resume.download', ['agency' => $otherAgency, 'application' => $response->json('application_id')]))->assertNotFound();
    }

    public function test_legacy_public_resume_can_still_be_downloaded_by_recruiter(): void
    {
        $application = $this->agency->applications()->create(['first_name' => 'Legacy']);
        $application->addMedia(UploadedFile::fake()->createWithContent('old.pdf', '%PDF-1.4'))->toMediaCollection('resumes', 'public');
        $user = User::factory()->create(['password' => 'test-password']);
        $user->agency()->attach($this->agency);
        $this->actingAs($user)->get(route('applications.resume.download', ['agency' => $this->agency, 'application' => $application]))->assertOk()->assertDownload('old.pdf');
    }

    public function test_published_jobs_are_paginated_and_have_plain_text_excerpts(): void
    {
        $this->job(['description' => '<p>Electrical &amp; installation</p>']);
        $this->job(['is_published' => 0]);
        $this->job(['agency_id' => Agency::factory()->create()->id]);
        $this->getJson('/api/v1/job-post/'.$this->agency->uuid)->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.description_excerpt', 'Electrical & installation')->assertJsonPath('meta.last_page', 1);
        $this->options('/api/v1/application/'.$this->agency->uuid, [], ['Origin' => 'https://joseline.example', 'Access-Control-Request-Method' => 'POST', 'Access-Control-Request-Headers' => 'accept'])
            ->assertSuccessful()->assertHeader('Access-Control-Allow-Origin', '*');
    }
}
