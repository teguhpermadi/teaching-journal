<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreStudentRequest;
use App\Http\Requests\Api\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;

class StudentController extends ApiController
{
    protected string $model = Student::class;
    protected array $filterable = ['active', 'gender'];
    protected array $searchable = ['name', 'nisn', 'nis', 'nick_name'];
    protected array $sortable = ['name', 'nis', 'birthday', 'created_at'];

    protected function getWith(): array
    {
        return ['grades'];
    }

    protected function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nick_name' => 'nullable|string|max:50',
            'city_born' => 'nullable|string|max:100',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'nisn' => 'nullable|string|max:20',
            'nis' => 'nullable|string|max:20',
            'photo' => 'nullable|string',
            'active' => 'boolean',
        ];
    }

    public function store(StoreStudentRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateStudentRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return StudentResource::class;
    }
}
