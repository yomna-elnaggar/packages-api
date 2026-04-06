<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Interfaces\CRUDRepositoryInterface::class,
            \App\Repositories\CRUDRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Auth::extend('jwt', function ($app, $name, array $config) {
            return new \Illuminate\Auth\RequestGuard(function ($request) {
                $token = $request->bearerToken();
                if (!$token) return null;

                try {
                    $parts = explode('.', $token);
                    if (count($parts) !== 3) return null;

                    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
                    $secret  = env('JWT_SECRET');

                    // Basic Signature Verification (consistent with VerifyAdminJwt)
                    $validSignature = hash_hmac('sha256', "$parts[0].$parts[1]", $secret, true);
                    $validSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

                    if (!hash_equals($validSignature, $parts[2])) return null;

                    if (isset($payload['exp']) && ($payload['exp'] + 60) < time()) return null;

                    // Return a GenericUser with the payload data so auth()->user()->company_id works
                    return new \Illuminate\Auth\GenericUser($payload);

                } catch (\Exception $e) {
                    return null;
                }
            }, $this->app['request']);
        });
    }
}
