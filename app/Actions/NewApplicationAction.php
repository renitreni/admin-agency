<?php

namespace App\Actions;

use App\Services\WorkerBlocklistService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class NewApplicationAction
{
    public static function process($request, $agency, $jobPostId = null)
    {
        // Check for blocklist matches before processing
        $workerData = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'middle_name' => $request->input('middle_name'),
            'email' => $request->input('email'),
            'contact_number' => $request->input('contact_number'),
            // passport_number, date_birth, place_birth not available in application
        ];

        $blocklistService = app(WorkerBlocklistService::class);
        $blocklistResult = $blocklistService->isBlocklisted($workerData, $agency->id);

        if ($blocklistResult['is_blocklisted']) {
            $match = $blocklistResult['matches'][0] ?? null;
            if ($match) {
                throw new \Illuminate\Validation\ValidationException(
                    \Illuminate\Validation\Validator::make([], []),
                    \Illuminate\Http\JsonResponse::class
                );
                // We'll handle this via a custom response in the controller
            }
        }

        // Isolate each upload so even a partial media-library write can be cleaned up.
        $uploadDirectory = 'cv-staging/'.Str::uuid();
        $disk = Storage::disk('private_cv');
        $media = null;

        try {
            $stagedPath = $request->file('resume')->storeAs($uploadDirectory, 'resume.'.$request->file('resume')->extension(), 'private_cv');

            return DB::transaction(function () use ($request, $agency, $jobPostId, $disk, $stagedPath, $uploadDirectory, &$media, $blocklistResult) {
                $application = $agency->applications()->create([
                    'job_post_id' => $jobPostId,
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'middle_name' => $request->input('middle_name'),
                    'contact_number' => $request->input('contact_number'),
                    'email' => $request->input('email'),
                    'cover_letter' => $request->input('cover_letter'),
                    'accepted_terms_and_condition' => $request->input('accepted_terms_and_condition'),
                ]);

                // Store blocklist match info on application if found
                if (! empty($blocklistResult['matches'])) {
                    $application->blocklist_matches = $blocklistResult['matches'];
                    $application->save();
                }

                $media = $application->addMedia($disk->path($stagedPath))
                    ->withCustomProperties(['upload_directory' => $uploadDirectory])
                    ->usingFileName('cv.'.$request->file('resume')->extension())
                    ->toMediaCollection('resumes', 'private_cv');

                return $application;
            });
        } catch (Throwable $exception) {
            if ($media) {
                $media->delete();
            }
            $disk->deleteDirectory(str_replace('cv-staging/', 'resumes/', $uploadDirectory));
            throw $exception;
        } finally {
            $disk->deleteDirectory($uploadDirectory);
        }
    }
}
