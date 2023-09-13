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
        NewApplicationAction::process($request, $agency);

        return response()->json(['message' => 'success']);
    }

    public function storeWithJob(ApplicationStoreRequest $request, Agency $agency, JobPost $jobPost)
    {
        NewApplicationAction::process($request, $agency, $jobPost->id);

        return response()->json(['message' => 'success']);
    }
}
