<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreTargetRequest;
use App\Http\Requests\Api\UpdateTargetRequest;
use App\Http\Resources\TargetResource;
use App\Models\Target;

class TargetController extends ApiController
{
    protected string $model = Target::class;
    protected array $filterable = ['academic_year_id', 'user_id', 'subject_id', 'grade_id', 'main_target_id'];
    protected array $searchable = ['target'];
    protected array $sortable = ['created_at'];

    protected function getWith(): array
    {
        return ['academicYear', 'user', 'subject', 'grade', 'mainTarget'];
    }

    protected function storeRules(): array
    {
        return [
            'user_id' => 'required|exists:users,ulid',
            'subject_id' => 'required|exists:subjects,ulid',
            'grade_id' => 'required|exists:grades,ulid',
            'academic_year_id' => 'required|exists:academic_years,ulid',
            'main_target_id' => 'nullable|exists:main_targets,ulid',
            'target' => 'required|string',
        ];
    }

    public function store(StoreTargetRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateTargetRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return TargetResource::class;
    }
}
