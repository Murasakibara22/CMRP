<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware CheckPermission
 *
 * Usage dans les routes :
 *   Route::middleware('permission:COTISATION_SHOW')->...
 *   Route::middleware('permission:COTISATION_SHOW,COTISATION_CREATE')->... (OR)
 *
 * Usage dans les composants Livewire :
 *   abort_unless(auth()->user()?->hasPermission('COTISATION_VALIDATE'), 403);
 *
 * Usage dans les blades :
 *   @if(auth()->user()?->hasPermission('COTISATION_VALIDATE'))
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): mixed
    {
        $user = auth()->user();

        if (! $user) {
            abort(401);
        }

        /* Vérifier au moins UNE des permissions passées (logique OR) */
        foreach ($permissions as $permission) {
            if ($user->hasPermission(trim($permission))) {
                return $next($request);
            }
        }

        abort(403, 'Action non autorisée.');
    }
}