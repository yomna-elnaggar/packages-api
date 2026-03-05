<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompanyService
{
    protected string $baseUrl;

    public function __construct()
    {
        // Placeholder URL - Should be set in .env
        $this->baseUrl = config('services.companies.url', 'http://companies-service/api');
    }

    public function getAllCompanies()
    {
        try {
            // Assuming there's an endpoint to get active companies
            $response = Http::get("{$this->baseUrl}/companies", ['active' => 1]);
            
            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
            
            Log::error("Failed to fetch companies: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("CompanyService Error: " . $e->getMessage());
            return [];
        }
    }

    public function getCompanyById($id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/companies/{$id}");
            
            if ($response->successful()) {
                return $response->json('data') ?? null;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("CompanyService Error: " . $e->getMessage());
            return null;
        }
    }
}
