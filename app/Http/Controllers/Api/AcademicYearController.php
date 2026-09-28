<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreAcademicYearRequest;
use App\Http\Requests\Api\UpdateAcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use App\SemesterEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicYearController extends ApiController
{
    protected string $model = AcademicYear::class;
    protected array $filterable = ['active'];
    protected array $searchable = ['year'];
    protected array $sortable = ['year', 'semester', 'active', 'created_at'];
    protected int $perPage = 10;

    protected function getWith(): array
    {
        return ['grades', 'subjects'];
    }

    protected function resource(): string
    {
        return AcademicYearResource::class;
    }

    public function store(StoreAcademicYearRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateAcademicYearRequest $request, string $id): JsonResponse
    {
        $item = $this->model::find($id);
        if (! $item) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $validated = $request->validated();
        $item->update($validated);
        return response()->json($this->resource()($item->fresh()->load($this->getWith())));
    }

    /** POST /academic-years/{id}/activate */
    public function activate(Request $request, string $id): JsonResponse
    {
        $year = AcademicYear::find($id);
        if (! $year) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $active = AcademicYear::setActive($id);
        return response()->json([
            'message' => 'Academic year activated',
            'active_year' => AcademicYearResource::make($active),
        ]);
    }
}