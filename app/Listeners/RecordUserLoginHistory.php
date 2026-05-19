<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UserLoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class RecordUserLoginHistory
{
    public function __construct(
        protected Request $request,
    ) {}

    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        UserLoginHistory::query()->create([
            'user_id' => $event->user->getKey(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}
