<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Services\CompanyPackageService;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\PackageResource;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Log;
use Exception;

class PackageController extends Controller
{
    private CompanyPackageService $packageService;

    public function __construct(CompanyPackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    // ─────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        try {
            $companyUser = auth()->user();
            $company_id  = $companyUser->company_id;

            $filters = $request->all();
            $filters['company_id'] = $company_id;

            $items = $this->packageService->getAllPackages($filters, $request->per_page ?? 20);

            $result = [
                'items' => SubscriptionResource::collection($items),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ],
            ];

            return ApiController::respondWithSuccess(
                'Packages retrieved successfully',
                $result
            );
        } catch (Exception $e) {
            Log::error('Company: Failed to retrieve packages', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                'Failed to retrieve packages',
                $e->getMessage(),
                500
            );
        }
    }

  
}
