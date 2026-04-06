<?php

namespace App\Services;

use App\Interfaces\CRUDRepositoryInterface;
use App\Models\CompanyPackage;
use Illuminate\Support\Facades\Log;
use Exception;

class CompanyPackageService
{
    private CRUDRepositoryInterface $itemRepository;
    private string $model = CompanyPackage::class;

    public function __construct(CRUDRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function getAllPackages(array $filters = [], int $perPage = 50)
    {
        // Add default recent ordering if not present
        return $this->itemRepository->getAllItems($this->model, $filters, $perPage, true, ['package']);
    }

    public function getPackageById(string $id)
    {
        return $this->itemRepository->getItemById($this->model, $id);
    }

    public function createPackage(array $data)
    {
        return $this->itemRepository->createItem($this->model, $data);
    }

    public function updatePackage(string $id, array $data)
    {
        return $this->itemRepository->updateItem($this->model, $id, $data);
    }

    public function deletePackage(string $id)
    {
        return $this->itemRepository->deleteItem($this->model, $id);
    }

    public function getTrashedPackages(array $filters = [], int $perPage = 50)
    {
        return $this->itemRepository->getTrashedItems($this->model, $filters, $perPage);
    }

    public function restorePackage(string $id)
    {
        return $this->itemRepository->restoreItem($this->model, $id);
    }

    public function forceDeletePackage(string $id)
    {
        return $this->itemRepository->forceDeleteItem($this->model, $id);
    }

    public function getCount(array $filters = [])
    {
        return $this->itemRepository->getCount($this->model, $filters);
    }
}
