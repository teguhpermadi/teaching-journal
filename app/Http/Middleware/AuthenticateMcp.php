<?php

namespace App\Http\Middleware;

use App\Models\McpToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMcp
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('mcp.token');
        $authorization = (string) $request->header('Authorization');
        $providedToken = str_starts_with($authorization, 'Bearer ')
            ? substr($authorization, 7)
            : (string) $request->header('X-MCP-Token');

        $token = null;
        if ($providedToken !== '') {
            $token = McpToken::query()
                ->with('user')
                ->where('token_hash', hash('sha256', $providedToken))
                ->first();
        }

        $validDatabaseToken = $token
            && ($token->expires_at === null || $token->expires_at->isFuture());
        $validStaticToken = $configuredToken !== ''
            && $providedToken !== ''
            && hash_equals($configuredToken, $providedToken);

        if (! $validDatabaseToken && ! $validStaticToken) {
            return response()->json([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32001,
                    'message' => 'MCP authentication failed.',
                ],
            ], 401);
        }

        if ($validDatabaseToken) {
            $token->forceFill(['last_used_at' => now()])->save();
            $request->attributes->set('mcp_token', $token);
            $request->setUserResolver(fn () => $token->user);
        }

        return $next($request);
    }
}
