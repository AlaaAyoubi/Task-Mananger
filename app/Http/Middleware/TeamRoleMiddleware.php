<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeamRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            abort(403, config('constants.error_messages.unauthorized'));
        }

        $user = Auth::user();
        
        // دعم multiple roles مفصولة بفواصل
        $roles = explode(',', $role);
        
        // التحقق من الدور في الفرق
        foreach ($roles as $singleRole) {
            $singleRole = trim($singleRole);
            $hasRole = $user->teams()->wherePivot('role', $singleRole)->exists();
            if ($hasRole) {
                return $next($request);
            }
        }
        
        abort(403, config('constants.error_messages.forbidden'));
    }
} 