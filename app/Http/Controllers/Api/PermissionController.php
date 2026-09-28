<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StorePermissionRequest;
use App\Http\Requests\Api\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends ApiController
{
    protected string $model = Permission::class;
    protected array $filterable = [];
    protected array $searchable = ['name'];
    protected array $sortable = ['name', 'created_at'];

    public function store(StorePermissionRequest $request): JsonResponse
    {
        return response()->json(['message' => 'Use Spatie permission management'], 405);
    }

    public function update(UpdatePermissionRequest $request, string $id): JsonResponse
    {
        return response()->json(['message' => 'Use Spatie permission management'], 405);
    }

    protected function resource(): string
    {
        return PermissionResource::class;
    }
}
