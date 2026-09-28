<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreTranscriptStudentRequest;
use App\Http\Requests\Api\UpdateTranscriptStudentRequest;
use App\Http\Resources\TranscriptStudentResource;
use App\Models\TranscriptStudent;

class TranscriptStudentController extends ApiController
{
    protected string $model = TranscriptStudent::class;
    protected array $filterable = ['transcript_id', 'academic_year_id', 'subject_id', 'grade_id', 'student_id'];
    protected array $searchable = [];
    protected array $sortable = ['score', 'created_at'];

    protected function getWith(): array
    {
        return ['transcript', 'student', 'academicYear', 'subject', 'grade'];
    }

    protected function storeRules(): array
    {
        return [
            'transcript_id' => 'required|exists:transcripts,ulid',
            'academic_year_id' => 'required|exists:academic_years,ulid',
            'subject_id' => 'required|exists:subjects,ulid',
            'grade_id' => 'required|exists:grades,ulid',
            'student_id' => 'required|exists:students,ulid',
            'score' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function store(StoreTranscriptStudentRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateTranscriptStudentRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return TranscriptStudentResource::class;
    }
}
