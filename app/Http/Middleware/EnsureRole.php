<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
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
        $user = $request->user();

        if (! $user || ! in_array($user->role->value, $roles, true)) {
            // Redirect to a sensible location based on their actual role
            if ($user?->role === UserRole::Agent || $user?->role === UserRole::Admin) {
                return to_route('agent.queue');
            }

            if ($user?->role === UserRole::Requester) {
                return to_route('requester.tickets.index');
            }

            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
