<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Exempt frontend AJAX/API endpoints used by the static frontend during development.
        'register',
        'login',
        'logout',
        'password/*',
        '_mail_test',
    ];
}
