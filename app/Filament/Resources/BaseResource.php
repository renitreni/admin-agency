<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

abstract class BaseResource extends Resource
{
    protected static function authUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    protected static function isFraUser(): bool
    {
        $user = static::authUser();

        return $user instanceof User && $user->user_type === User::TYPE_FRA;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && ! static::isFraUser();
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && ! static::isFraUser();
    }
}
