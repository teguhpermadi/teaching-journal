<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    /** @var string Model class */
    protected string $model;

    /** @var string[] Columns allowed for simple filter (=) */
    protected array $filterable = [];

    /** @var string[] Columns allowed for search (LIKE) */
    protected array $searchable = [];

    /** @var string[] Columns allowed for sort */
    protected array $sortable = [];

    /** @var int Per-page default */
    protected int $perPage = 20;

    /**
     * GET /resource — paginated list with filters, search, sort.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query()->with($this->getWith());

        foreach ($this->filterable as $col) {
            if ($value = $request->query($col)) {
                $query->where($col, $value);
            }
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                foreach ($this->searchable as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        if (in_array($sortBy, $this->sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $data = $query->paginate($request->query('per_page', $this->perPage));

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

    /**
     * GET /resource/{id}
     */
    public function show(string $id): JsonResponse
    {
        $item = $this->model::with($this->getWith())->find($id);

        if (! $item) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($this->resource()($item));
    }

    /**
     * DELETE /resource/{id} — soft delete
     */
    public function destroy(string $id): JsonResponse
    {
        $item = $this->model::find($id);
        if (! $item) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Deleted', 'id' => $id]);
    }

    /**
     * PUT /resource/{id}/restore — restore soft-deleted
     */
    public function restore(string $id): JsonResponse
    {
        $item = $this->model::onlyTrashed()->find($id);
        if (! $item) {
            return response()->json(['message' => 'Not found or not trashed'], 404);
        }

        $item->restore();

        return response()->json(['message' => 'Restored', 'id' => $id]);
    }

    /** Eloquent relations to eager-load */
    protected function getWith(): array
    {
        return [];
    }

    /** Resource class for this controller */
    abstract protected function resource(): string;
}