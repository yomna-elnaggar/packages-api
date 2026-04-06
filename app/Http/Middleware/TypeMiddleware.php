<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TypeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Use isset to avoid 'Undefined array key' on GenericUser
        $userTypeId = ($user && isset($user->user_type_id)) ? $user->user_type_id : null;

        // Check if the user is a company type (ID 1 based on UserTypesTableSeeder)
        if (!$user || $userTypeId != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: This action requires a company account.',
            ], 403);
        }

        return $next($request);
    }
}
