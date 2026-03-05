<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check header request and determine localization
        $header = $request->header('Accept-Language');
        $locales = explode(',', $header);
        $local = count($locales) > 0 ? trim($locales[0]) : app()->getLocale();

        // Basic normalization (e.g., en-US -> en)
        if (str_contains($local, '-')) {
            $local = explode('-', $local)[0];
        }

        // set app locale
        App::setLocale($local);

        return $next($request);
    }
}
