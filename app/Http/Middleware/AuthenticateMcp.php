<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validDatabaseToken = false;

        if ($providedToken !== '') {
            // Cek Sanctum token dengan type 'mcp'
            $token = \Laravel\Sanctum\PersonalAccessToken::findToken($providedToken);

            if ($token
                && $token->type === 'mcp'
                && $token->tokenable_type === \App\Models\User::class
                && ($token->expires_at === null || $token->expires_at->isFuture())
            ) {
                $validDatabaseToken = true;
                $token->forceFill(['last_used_at' => now()])->save();
                $request->setUserResolver(fn () => $token->tokenable);
            }
        }

        $validStaticToken = $configuredToken !== ''
            && $providedToken !== ''
            && hash_equals($configuredToken, $providedToken);

        if ($validStaticToken && config('mcp.static_user_id')) {
            $staticUser = \App\Models\User::query()->find(config('mcp.static_user_id'));
            if ($staticUser) {
                $request->setUserResolver(fn () => $staticUser);
            }
        }

        if (! $validDatabaseToken && ! $validStaticToken) {
            return response()->json([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32001,
                    'message' => 'MCP authentication failed.',
                ],
            ], 401);
        }

        $request->attributes->set('mcp_token', $token);

        return $next($request);
    }
}
