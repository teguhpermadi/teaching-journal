<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreSignatureRequest;
use App\Http\Requests\Api\UpdateSignatureRequest;
use App\Http\Resources\SignatureResource;
use App\Models\Signature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignatureController extends ApiController
{
    protected string $model = Signature::class;
    protected array $filterable = ['journal_id', 'signer_id', 'signer_role'];
    protected array $searchable = [];
    protected array $sortable = ['signed_at', 'created_at'];

    protected function getWith(): array
    {
        return ['journal', 'signer'];
    }

    protected function storeRules(): array
    {
        return [
            'journal_id' => 'required|exists:journals,ulid',
            'signer_id' => 'required|exists:users,ulid',
            'signer_role' => 'required|string|max:50',
            'signature_path' => 'nullable|string',
            'signature_base64' => 'nullable|string',
            'signed_at' => 'nullable|date',
        ];
    }

    public function store(StoreSignatureRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $item = $this->model::create($validated);
        return response()->json($this->resource()($item->load($this->getWith())), 201);
    }

    public function update(UpdateSignatureRequest $request, string $id): \Illuminate\Http\JsonResponse
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
        return SignatureResource::class;
    }

    /** POST /signatures/upload — store signature with base64 file */
    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'journal_id' => 'required|exists:journals,ulid',
            'signer_id' => 'required|exists:users,ulid',
            'signer_role' => 'required|string|max:50',
            'signature_base64' => 'required|string',
        ]);

        $data = $validated;
        unset($data['signature_base64']);
        $data['signed_at'] = now();

        $signature = Signature::create($data);

        return response()->json($this->resource()($signature), 201);
    }
}
