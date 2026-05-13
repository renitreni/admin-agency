<?php

namespace App\Services;

use App\Models\Worker;

class WorkerService
{
    public static function generateCode(): string
    {
        do {
            $code = (string) random_int(10000, 99999);
            $exists = Worker::query()->where('code', $code)->exists();
        } while ($exists);

        return $code;
    }

    public static function regenerateCode(Worker $worker): string
    {
        $code = static::generateCode();

        $worker->forceFill([
            'code' => $code,
        ])->save();

        return $code;
    }
}
