<?php

use App\Http\Controllers\McpAuthController;
use App\Http\Middleware\AuthenticateMcp;
use Illuminate\Support\Facades\Route;

Route::post('/mcp/login', [McpAuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('mcp.login');

Route::post('/mcp/logout', [McpAuthController::class, 'logout'])
    ->middleware(AuthenticateMcp::class)
    ->name('mcp.logout');
