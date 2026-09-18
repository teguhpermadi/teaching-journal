<?php

return [
    'token' => env('MCP_TOKEN'),
    'static_user_id' => env('MCP_STATIC_USER_ID'),
    'token_ttl' => (int) env('MCP_TOKEN_TTL', 1440),
    'allow_mutations' => (bool) env('MCP_ALLOW_MUTATIONS', false),
    'max_page_size' => (int) env('MCP_MAX_PAGE_SIZE', 100),
];
