<?php

namespace App\Http\Controllers\API;

use App\Actions\NewApplicationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationStoreRequest;
use App\Models\Agency;
use App\Models\JobPost;

class ApplicationController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'success']);
    }

    public function store(ApplicationStoreRequest $request, Agency $agency)
    {
        $job = $request->filled('job_id')
            ? $agency->jobPosts()->published()->where('uuid', $request->input('job_id'))->firstOrFail()
            : null;

        return $this->submit($request, $agency, $job);
    }

    public function storeWithJob(ApplicationStoreRequest $request, Agency $agency, JobPost $jobPost)
    {
        return $this->submit($request, $agency, $jobPost);
    }

    private function submit(ApplicationStoreRequest $request, Agency $agency, ?JobPost $job)
    {
        $application = NewApplicationAction::process($request, $agency, $job?->id);

        return response()->json([
            'message' => 'Your CV has been submitted. Our recruitment team will contact you if there is a suitable opportunity.',
            'application_id' => $application->id,
        ], 201);
    }
}
