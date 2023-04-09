<?php

namespace App\Http\Middleware;

use App\Traits\JsonResponse as JResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyAdminRole
{
    use JResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next, string $role): JsonResponse
    {
        $roles = explode('|', $role);

        $user = $request->user();

        if (!$user->roles()->whereIn('name', $roles)->exists()) {
            return $this->error(
                'You are not authorized to carry out this action',
                \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
