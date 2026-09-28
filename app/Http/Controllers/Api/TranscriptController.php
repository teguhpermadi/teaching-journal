<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreTranscriptRequest;
use App\Http\Requests\Api\UpdateTranscriptRequest;
use App\Http\Resources\TranscriptResource;
use App\Models\Transcript;

class TranscriptController extends ApiController
{
    protected string $model = Transcript::class;
    protected array $filterable = ['grade_id', 'subject_id', 'academic_year_id', 'user_id'];
    protected array $searchable = ['title', 'description'];
    protected array $sortable = ['title', 'created_at'];

    protected function getWith(): array
    {
        return ['grade', 'subject', 'journal', 'academicYear', 'user', 'transcriptStudents'];
    }

    protected function storeRules(): array
    {
        return [
            'grade_id' => 'required|exists:grades,ulid',
            'subject_id' => 'required|exists:subjects,ulid',
            'journal_id' => 'nullable|exists:journals,ulid',
            'academic_year_id' => 'required|exists:academic_years,ulid',
            'user_id' => 'required|exists:users,ulid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function store(StoreTranscriptRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateTranscriptRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return TranscriptResource::class;
    }
}
