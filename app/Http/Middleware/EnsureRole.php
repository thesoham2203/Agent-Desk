<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ============================================================
 * FILE: EnsureRole.php
 * LAYER: Middleware
 * ============================================================
 *
 * WHAT IS THIS?
 * A middleware that restricts access to routes based on user roles.
 *
 * WHY DOES IT EXIST?
 * To ensure that only authorized users (e.g., Admins, Agents) can
 * access specific parts of the application.
 *
 * HOW IT FITS IN THE APP:
 * Registered in bootstrap/app.php and used in route groups in
 * routes/web.php.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Middleware provide a convenient mechanism for inspecting and
 * filtering HTTP requests entering your application.
 * ============================================================
 */
final class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_if(! $request->user() || ! in_array($request->user()->role->value, $roles, true), 403, 'Unauthorized action.');

        return $next($request);
    }
}
