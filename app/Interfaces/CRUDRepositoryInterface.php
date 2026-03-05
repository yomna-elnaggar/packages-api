<?php

namespace App\Interfaces;

interface CRUDRepositoryInterface
{
    public function getAllItems($model, array $filters = [], $perPage = 50, $latest = true);
    public function getItemById($model, $id);
    public function createItem($model, array $data);
    public function updateItem($model, $id, array $data);
    public function deleteItem($model, $id);
    public function toggleStatus($model, $id, $column = 'active');
    public function getCount($model, array $filters = []);
}
