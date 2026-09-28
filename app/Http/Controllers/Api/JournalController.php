<?php

namespace App\Http\Controllers\Api;

use App\TeachingStatusEnum;
use App\Http\Requests\Api\StoreJournalRequest;
use App\Http\Requests\Api\UpdateJournalRequest;
use App\Http\Resources\JournalResource;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalController extends ApiController
{
    protected string $model = Journal::class;
    protected array $filterable = ['subject_id', 'grade_id', 'user_id', 'status'];
    protected array $searchable = ['chapter', 'activity', 'notes'];
    protected array $sortable = ['date', 'status', 'created_at'];
    protected int $perPage = 15;

    protected function getWith(): array
    {
        return ['subject', 'grade', 'user', 'signatures'];
    }

    protected function storeRules(): array
    {
        return [
            'academic_year_id' => 'required|exists:academic_years,ulid',
            'subject_id' => 'required|exists:subjects,ulid',
            'grade_id' => 'required|exists:grades,ulid',
            'user_id' => 'required|exists:users,ulid',
            'date' => 'required|date',
            'main_target_id' => 'nullable|string',
            'target_id' => 'nullable|array',
            'chapter' => 'nullable|string',
            'activity' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:'.implode(',', array_map(fn($s) => $s->value, TeachingStatusEnum::cases())),
        ];
    }

    public function store(StoreJournalRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateJournalRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return JournalResource::class;
    }

    /** GET /journals/my — journals for authenticated user */
    public function myJournals(Request $request): JsonResponse
    {
        $userId = $request->user()?->getKey();
        if (! $userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $this->model::query()
            ->where('user_id', $userId)
            ->with($this->getWith())
            ->when($request->query('search'), fn ($q, $search) => $q->where(function ($qq) use ($search) {
                foreach ($this->searchable as $col) {
                    $qq->orWhere($col, 'like', "%{$search}%");
                }
            }))
            ->paginate($request->query('per_page', $this->perPage));

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
