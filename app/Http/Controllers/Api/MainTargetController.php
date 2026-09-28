<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreMainTargetRequest;
use App\Http\Requests\Api\UpdateMainTargetRequest;
use App\Http\Resources\MainTargetResource;
use App\Models\MainTarget;

class MainTargetController extends ApiController
{
    protected string $model = MainTarget::class;
    protected array $filterable = ['academic_year_id', 'user_id', 'subject_id', 'grade_id'];
    protected array $searchable = ['main_target'];
    protected array $sortable = ['created_at'];

    protected function getWith(): array
    {
        return ['academicYear', 'user', 'subject', 'grade', 'targets'];
    }

    protected function storeRules(): array
    {
        return [
            'academic_year_id' => 'required|exists:academic_years,ulid',
            'user_id' => 'required|exists:users,ulid',
            'subject_id' => 'required|exists:subjects,ulid',
            'grade_id' => 'required|exists:grades,ulid',
            'main_target' => 'required|string',
        ];
    }

    public function store(StoreMainTargetRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateMainTargetRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return MainTargetResource::class;
    }
}
