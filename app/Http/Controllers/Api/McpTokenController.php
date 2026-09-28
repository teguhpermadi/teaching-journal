<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreMcpTokenRequest;
use App\Http\Requests\Api\UpdateMcpTokenRequest;
use App\Http\Resources\McpTokenResource;
use App\Models\McpToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class McpTokenController extends ApiController
{
    protected string $model = McpToken::class;
    protected array $filterable = ['user_id'];
    protected array $searchable = [];
    protected array $sortable = ['expires_at', 'last_used_at', 'created_at'];

    public function store(StoreMcpTokenRequest $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Use MCP login to create tokens'], 405);
    }

    public function update(UpdateMcpTokenRequest $request, string $id): JsonResponse
    {
        return response()->json(['message' => 'Not allowed'], 405);
    }

    protected function resource(): string
    {
        return McpTokenResource::class;
    }

    /** DELETE /mcp-tokens/{id} — revoke token (alias for destroy) */
    public function revoke(string $id): JsonResponse
    {
        $token = McpToken::find($id);
        if (! $token) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $token->delete();
        return response()->json(['message' => 'Token revoked', 'id' => $id]);
    }
}
