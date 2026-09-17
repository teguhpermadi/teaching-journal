<?php

return [
    'token' => env('MCP_TOKEN'),
    'token_ttl' => (int) env('MCP_TOKEN_TTL', 1440),
    'allow_mutations' => (bool) env('MCP_ALLOW_MUTATIONS', false),
    'max_page_size' => (int) env('MCP_MAX_PAGE_SIZE', 100),
];
