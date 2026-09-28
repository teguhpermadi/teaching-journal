<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreRoleRequest;
use App\Http\Requests\Api\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends ApiController
{
    protected string $model = Role::class;
    protected array $filterable = [];
    protected array $searchable = ['name'];
    protected array $sortable = ['name', 'created_at'];

    public function store(StoreRoleRequest $request): JsonResponse
    {
        return response()->json(['message' => 'Use Spatie role management'], 405);
    }

    public function update(UpdateRoleRequest $request, string $id): JsonResponse
    {
        return response()->json(['message' => 'Use Spatie role management'], 405);
    }

    protected function resource(): string
    {
        return RoleResource::class;
    }
}
