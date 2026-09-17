<?php

namespace App\Http\Controllers;

use App\Services\Mcp\McpServer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class McpController extends Controller
{
    public function __invoke(Request $request, McpServer $server): JsonResponse
    {
        $payload = $request->json()->all();

        if (! is_array($payload) || array_is_list($payload)) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id' => null,
                'error' => ['code' => -32600, 'message' => 'A single JSON-RPC object is required.'],
            ], 400);
        }

        $response = $server->handle($payload);

        if ($response === null) {
            return response()->json([], 202);
        }

        return response()->json($response)->header('MCP-Protocol-Version', '2025-06-18');
    }
}
