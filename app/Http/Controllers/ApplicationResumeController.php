<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationResumeController extends Controller
{
    public function __invoke(Request $request, Agency $agency, Application $application)
    {
        $user = $request->user();
        abort_unless($user && $user->user_type !== User::TYPE_FRA
            && $user->agency()->where('agencies.id', $agency->id)->exists(), 403);
        abort_unless($application->agency_id === $agency->id, 404);

        $media = $application->getFirstMedia('resumes');
        abort_unless($media, 404);
        $disk = Storage::disk($media->disk);
        $path = $media->getPathRelativeToRoot();
        abort_unless($disk->exists($path), 404);

        return $disk->download($path, $media->file_name, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }
}
