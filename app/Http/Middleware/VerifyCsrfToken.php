<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/api/profile',
        '/api/experiences',
        '/api/courses',
        '/api/projects'
    ];
}
