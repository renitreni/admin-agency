<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobPostResource;
use App\Http\Resources\SingleJobPostResource;
use App\Models\Agency;
use App\Models\JobPost;

class JobController extends Controller
{
    public function index(Agency $agency)
    {
        $jobPost = JobPost::where('agency_id', $agency->id)
            ->published()
            ->paginate(5);

        return JobPostResource::collection($jobPost);
    }

    public function show(JobPost $jobPost)
    {
        return SingleJobPostResource::make($jobPost);
    }
}
