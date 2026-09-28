<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

return [
    'api_path' => 'api',
    'api_domain' => null,
    'export_path' => 'api.json',

    'cache' => [
        'key' => 'scramble.openapi',
        'store' => 'file',
    ],

    'info' => [
        'version' => env('API_VERSION', '1.0.0'),
        'description' => 'Teaching Journal API — Auto-generated docs.\n\nBase URL: `/api`. Gunakan Bearer Token (MCP login) untuk endpoint bertanda 🔒.\n\n**Enum:** GET /api/enums — daftar nilai enum.',
    ],

    'ui' => [
        'title' => 'Teaching Journal API Docs',
    ],

    'dev_tools' => [
        'enabled' => env('SCRAMBLE_DEV_TOOLS', env('APP_DEBUG', false)),
    ],

    'renderer' => 'elements',

    'renderers' => [
        'elements' => [
            'view' => 'scramble::docs',
            'theme' => 'light',
            'hideTryIt' => false,
            'hideSchemas' => false,
            'logo' => '',
            'tryItCredentialsPolicy' => 'include',
            'layout' => 'responsive',
            'router' => 'hash',
        ],
    ],

    'servers' => null,
    'enum_cases_description_strategy' => 'description',
    'enum_cases_names_strategy' => false,
    'flatten_deep_query_parameters' => true,

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    'extensions' => [],

    'security_strategy' => [
        \Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy::class,
        [
            'middleware' => ['AuthenticateMcp'],
            'scheme' => SecurityScheme::http('bearer'),
        ],
    ],
];
