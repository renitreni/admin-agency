<?php

namespace App\Services;

use App\Models\Worker;
use Illuminate\Support\Str;

class WorkerService
{
    public static function generateCode()
    {
        do {
            $code = Str::upper(Str::random(5));
            $exists = Worker::query()->where('code', $code)->exists();
        } while ($exists);

        return $code;
    }
}
