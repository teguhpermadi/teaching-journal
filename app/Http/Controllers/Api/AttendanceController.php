<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreAttendanceRequest;
use App\Http\Requests\Api\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;

class AttendanceController extends ApiController
{
    protected string $model = Attendance::class;
    protected array $filterable = ['student_id', 'status'];
    protected array $searchable = [];
    protected array $sortable = ['date', 'created_at'];

    protected function getWith(): array
    {
        return ['student'];
    }

    protected function storeRules(): array
    {
        return [
            'student_id' => 'required|exists:students,ulid',
            'date' => 'required|date',
            'status' => 'required|string',
        ];
    }

    public function store(StoreAttendanceRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateAttendanceRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return AttendanceResource::class;
    }

    /** GET /attendance/student/{studentId} — attendance history for a student */
    public function studentAttendance(string $studentId): JsonResponse
    {
        $data = $this->model::query()
            ->where('student_id', $studentId)
            ->with($this->getWith())
            ->orderBy('date', 'desc')
            ->paginate($this->perPage);

        return response()->json([
            'data' => $this->resource()::collection($data->items()),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }
}
