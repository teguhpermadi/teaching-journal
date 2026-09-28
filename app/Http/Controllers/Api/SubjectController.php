<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreSubjectRequest;
use App\Http\Requests\Api\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;

class SubjectController extends ApiController
{
    protected string $model = Subject::class;
    protected array $filterable = ['academic_year_id', 'grade_id', 'user_id'];
    protected array $searchable = ['name', 'code'];
    protected array $sortable = ['name', 'code', 'created_at'];

    protected function getWith(): array
    {
        return ['grade', 'academicYear', 'user'];
    }

    protected function storeRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,ulid',
            'grade_id' => 'required|exists:grades,ulid',
            'academic_year_id' => 'required|exists:academic_years,ulid',
            'color' => 'nullable|string|max:7',
        ];
    }

    public function store(StoreSubjectRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateSubjectRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return SubjectResource::class;
    }
}
