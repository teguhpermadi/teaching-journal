<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreGradeRequest;
use App\Http\Requests\Api\UpdateGradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;

class GradeController extends ApiController
{
    protected string $model = Grade::class;
    protected array $filterable = ['academic_year_id'];
    protected array $searchable = ['name'];
    protected array $sortable = ['name', 'level', 'created_at'];

    protected function getWith(): array
    {
        return ['academicYear'];
    }

    protected function storeRules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'level' => 'required|integer|min:1|max:6',
            'academic_year_id' => 'required|exists:academic_years,ulid',
        ];
    }

    public function store(StoreGradeRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateGradeRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return GradeResource::class;
    }
}
