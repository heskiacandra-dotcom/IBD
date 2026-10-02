<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as BaseAuthenticate;

class FilamentAuthenticate extends BaseAuthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * Redirects to the unified single SSO login portal.
     */
    protected function redirectTo($request): ?string
    {
        return route('login');
    }
}
