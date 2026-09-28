<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreSocialiteUserRequest;
use App\Http\Requests\Api\UpdateSocialiteUserRequest;
use App\Http\Resources\SocialiteUserResource;
use App\Models\SocialiteUser;

class SocialiteUserController extends ApiController
{
    protected string $model = SocialiteUser::class;
    protected array $filterable = ['provider', 'user_id'];
    protected array $searchable = ['provider_id'];
    protected array $sortable = ['created_at'];

    protected function getWith(): array
    {
        return ['user'];
    }

    protected function storeRules(): array
    {
        return [
            'user_id' => 'required|exists:users,ulid',
            'provider' => 'required|string|max:50',
            'provider_id' => 'required|string',
        ];
    }

    public function store(StoreSocialiteUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateSocialiteUserRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        $item = $this->model::find($id);
        if (! $item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $validated = $request->validated();
        $item->update($validated);
        return response()->json($this->resource()($item->fresh()->load($this->getWith())));
    }

    protected function resource(): string
    {
        return SocialiteUserResource::class;
    }
}
