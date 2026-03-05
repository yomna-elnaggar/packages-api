<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Http\Resources\PackageResource;
use App\Interfaces\CRUDRepositoryInterface;
use App\Http\Requests\PackageRequest;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class PackageController extends Controller
{
    private CRUDRepositoryInterface $itemRepository;
    private string $model = Package::class;

    public function __construct(CRUDRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function index(Request $request)
    {
        try {
            $filters = [
                'search' => $request->search ?? null,
                'active' => $request->active ?? null,
            ];

            $items = $this->itemRepository->getAllItems($this->model, $filters , 20 );
            $counts = $this->itemRepository->getCount($this->model, $filters);

            $result = [
                'items' => PackageResource::collection($items)->response()->getData(true),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ],
                'counts' => $counts,
            ];

            return ApiController::respondWithSuccess(
                __('messages.packages_retrieved'),
                $result
            );
        } catch (Exception $e) {
            Log::error('Failed to retrieve packages', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                __('messages.package_retrieve_failed'),
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
                __('messages.package_retrieved'),
                new PackageResource($item)
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                __('messages.package_not_found'),
                $e->getMessage(),
                404
            );
        }
    }

    public function store(PackageRequest $request)
    {
        try {
            $data = $request->validated();

            $item = $this->itemRepository->createItem($this->model, $data);

            return ApiController::respondWithSuccess(
                __('messages.package_created'),
                new PackageResource($item)
            );
        } catch (Exception $e) {
            Log::error('Failed to create package', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                __('messages.package_create_failed'),
                $e->getMessage(),
                500
            );
        }
    }

    public function edit($id)
    {
        try {
            $item = $this->itemRepository->getItemById($this->model, $id);

            $result = [
                'item' => new PackageResource($item),
            ];

            return ApiController::respondWithSuccess(
                __('messages.form_data_retrieved'),
                $result
            );
        } catch (Exception $e) {
            return ApiController::respondWithError(
                __('messages.package_not_found'),
                $e->getMessage(),
                404
            );
        }
    }

    public function update(PackageRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $package = $this->itemRepository->updateItem($this->model, $id, $data);

            return ApiController::respondWithSuccess(
                __('messages.package_updated'),
                new PackageResource($package)
            );
        } catch (Exception $e) {
            Log::error('Failed to update package', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                __('messages.package_update_failed'),
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
                __('messages.package_deleted'),
                null
            );
        } catch (Exception $e) {
            Log::error('Failed to delete package', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ApiController::respondWithError(
                __('messages.package_delete_failed'),
                $e->getMessage(),
                500
            );
        }
    }
}
