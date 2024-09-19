<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\UserRoleEnums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{

    public function handle(Request $request, Closure $next,$role): Response
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user->role == $role) {

            return $next($request);
        }
        return response()->json(['message', 'you are not authenticated']);
    }
}
