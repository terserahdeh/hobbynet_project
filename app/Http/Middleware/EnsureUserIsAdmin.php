<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets only users with users.is_admin = 1 through.
 * Used by class name in routes/admin.php, so it needs no registration.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_admin, 403, 'This area is for admins only.');

        return $next($request);
    }
}
