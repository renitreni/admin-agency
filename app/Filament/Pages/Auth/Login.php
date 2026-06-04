<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament.components.layout.auth-login';

    protected ?string $maxWidth = MaxWidth::Large->value;

    public function hasLogo(): bool
    {
        return false;
    }

    public function getSubHeading(): string | Htmlable | null
    {
        return 'Sign in to manage your agency';
    }
}
