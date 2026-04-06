<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Use isset to avoid 'Undefined array key' on GenericUser
        $companyId = ($user && isset($user->company_id)) ? $user->company_id : null;

        // Check if user has a company_id
        if (!$user || !$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: This user is not associated with any company.',
            ], 403);
        }

        return $next($request);
    }
}
