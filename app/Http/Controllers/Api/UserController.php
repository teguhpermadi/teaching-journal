<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiController
{
    protected string $model = User::class;
    protected array $filterable = [];
    protected array $searchable = ['name', 'email'];
    protected array $sortable = ['name', 'email', 'last_login_at', 'created_at'];
    protected int $perPage = 20;

    public function store(StoreUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item), 201);
    }

    public function update(UpdateUserRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $item = $this->model::find($id);
        if (! $item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $validated = $request->validated();
        if (isset($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $item->update($validated);
        return response()->json($this->resource()($item->fresh()));
    }

    protected function resource(): string
    {
        return UserResource::class;
    }

    /** GET /users/me — current authenticated user */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json($this->resource()($user));
    }
}
