<?php

namespace App\Http\Controllers;

use App\Models\CompanyPackage;
use App\Models\Package;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\PackageResource;
use App\Interfaces\CRUDRepositoryInterface;
use App\Services\CompanyService;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class SubscriptionController extends Controller
{
    private CompanyService $companyService;
    private CRUDRepositoryInterface $itemRepository;
    private string $model = CompanyPackage::class;

    public function __construct(
        CompanyService $companyService,
        CRUDRepositoryInterface $itemRepository
    ) 
    {
        $this->companyService = $companyService;
        $this->itemRepository = $itemRepository;
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'company_id', 'package_id', 'expires_at', 'active', 'from_date', 'to_date']);

            $items = $this->itemRepository->getAllItems($this->model, $filters, $request->per_page ?? 50, true, ['package']);
            $counts = $this->itemRepository->getCount($this->model, $filters);
            $packages = Package::active()->get();

            $items = $this->itemRepository->getAllItems($this->model, $filters, 50);
            $items->load('package'); 

            // if ($request->action === 'export') {
            //     return Excel::download(
            //         new VehicleBrandExport($items),
            //         'subscriptions.xlsx'
            //     );
            // }
            $counts = $this->itemRepository->getCount($this->model, $filters);

            $companies = $this->companyService->getAllCompanies();

            $result = [
                'packages' => PackageResource::collection($packages),
                'companies' => $companies,
                'items' => SubscriptionResource::collection($items),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'last_page' => $items->lastPage(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem(),
                    'next_page' => $items->nextPageUrl(),
                    'prev_page' => $items->previousPageUrl(),
                ],
                'counts' => $counts,
            ];

            return ApiController::respondWithSuccess(
                __('messages.subscriptions_retrieved'),
                $result
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve subscriptions', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                __('messages.subscription_retrieve_failed'),
                $e->getMessage(),
                500
            );
        }
    }

    public function show($id)
    {
        try {
            $item = $this->itemRepository->getItemById($this->model, $id);

            return ApiController::respondWithSuccess(
                __('messages.subscription_retrieved'),
                new SubscriptionResource($item->load('package'))
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                __('messages.subscription_retrieve_failed'),
                $e->getMessage(),
                500
            );
        }
    }

    public function create()
    {
        try {
            $packages = Package::active()->get();
            $companies = $this->companyService->getAllCompanies();

            $result = [
                'packages' => PackageResource::collection($packages),
                'companies' => $companies,
            ];

            return ApiController::respondWithSuccess(
                __('messages.form_data_retrieved'),
                $result
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                __('messages.form_data_failed'),
                $e->getMessage(),
                500
            );
        }
    }

    public function store(SubscriptionRequest $request)
    {
        try {
            $data = $request->validated();

            $company = $this->companyService->getCompanyById($data['company_id']);
            if (!$company) {
                return ApiController::respondWithError(
                    __('messages.company_not_found'),
                    null,
                    404
                );
            }

            $package = Package::findOrFail($data['package_id']);

            $totalPrice = $data['num_of_cars'] * $package->price;

            if ($data['payment_status'] == 'paid') {
                $subscribedAt = Carbon::today();
                $expiresAt = $subscribedAt->copy()->addDays($package->duration);
            } else {
                $subscribedAt = null;
                $expiresAt = null;
            }

            $subscription = $this->itemRepository->createItem($this->model, [
                'company_id' => $data['company_id'],
                'package_id' => $data['package_id'],
                'subscribed_at' => $data['subscribed_at'] ?? $subscribedAt,
                'expires_at' => $data['expires_at'] ?? $expiresAt,
                'num_of_cars' => $data['num_of_cars'],
                'price' => $data['price'] ?? $totalPrice,
                'price_with_tax' => $data['price_with_tax'] ?? $data['total_price_with_tax'] ?? ($data['price'] ?? $totalPrice) * 1.15,
                'payment_status' => $data['payment_status'],
            ]);

            return ApiController::respondWithSuccess(
                __('messages.subscription_created'),
                new SubscriptionResource($subscription->load('package'))
            );
        } catch (Exception $e) {
            Log::error('Failed to create subscription', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                __('messages.subscription_create_failed'),
                $e->getMessage(),
                500
            );
        }
    }

    public function edit($id)
    {
        try {
            $packages = Package::active()->get();
            $companies = $this->companyService->getAllCompanies();
            $item = $this->itemRepository->getItemById($this->model, $id);
            $item->load('package');

            $result = [
                'packages' => PackageResource::collection($packages),
                'companies' => $companies,
                'item' => new SubscriptionResource($item),
            ];

            return ApiController::respondWithSuccess(
                __('messages.form_data_retrieved'),
                $result
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                __('messages.form_data_failed'),
                $e->getMessage(),
                500
            );
        }
    }

    public function update(SubscriptionRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $subscription = $this->itemRepository->getItemById($this->model, $id);

            /*
            if (isset($data['company_id'])) {
                $company = $this->companyService->getCompanyById($data['company_id']);
                if (!$company) {
                    return ApiController::respondWithError(
                        __('messages.company_not_found'),
                        null,
                        404
                    );
                }
            }
            */

            // Recalculate price if package or vehicle count changes
            if (isset($data['package_id']) || isset($data['num_of_cars'])) {
                $packageId = $data['package_id'] ?? $subscription->package_id;
                $numOfCars = $data['num_of_cars'] ?? $subscription->num_of_cars;
                $package = Package::findOrFail($packageId);

                $totalPrice = $numOfCars * $package->price;
                $data['price'] = $totalPrice;
                $data['price_with_tax'] = $data['price_with_tax'] ?? $totalPrice * 1.15;

                if (isset($data['package_id'])) {
                    $subscribedAt = $subscription->subscribed_at ?? Carbon::now();
                    $data['expires_at'] = $data['expires_at'] ?? Carbon::parse($subscribedAt)->addDays($package->duration);
                }
            }

            $subscription->update($data);

            return ApiController::respondWithSuccess(
                __('messages.subscription_updated'),
                new SubscriptionResource($subscription->load('package'))
            );
        } catch (Exception $e) {
            Log::error('Failed to update subscription', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                __('messages.subscription_update_failed'),
                $e->getMessage(),
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $this->itemRepository->deleteItem($this->model, $id);

            return ApiController::respondWithSuccess(
                __('messages.subscription_deleted'),
                null
            );
        } catch (\Exception $e) {
            return ApiController::respondWithError(
                __('messages.subscription_delete_failed'),
                $e->getMessage(),
                500
            );
        }
    }
}
