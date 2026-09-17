<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'mcp.token' => 'test-mcp-token',
        'mcp.allow_mutations' => false,
    ]);
});

function mcpRequest(array $payload)
{
    return test()->withHeader('Authorization', 'Bearer test-mcp-token')
        ->postJson('/mcp', $payload);
}

test('rejects requests without the MCP token', function () {
    test()->postJson('/mcp', [
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'ping',
    ])->assertUnauthorized();
});

test('can login through the API and use the issued bearer token', function () {
    Role::create([
        'name' => 'teacher',
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create([
        'email' => 'mcp@example.com',
        'password' => 'secret-password',
    ]);

    $login = test()->postJson('/api/mcp/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertOk()
        ->assertJsonPath('token_type', 'Bearer');

    $token = $login->json('access_token');

    expect($token)->toBeString()->not->toBeEmpty();

    test()->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/mcp', [
            'jsonrpc' => '2.0',
            'id' => 11,
            'method' => 'ping',
        ])->assertOk()
        ->assertJsonPath('result', []);
});

test('rejects invalid MCP API credentials', function () {
    test()->postJson('/api/mcp/login', [
        'email' => 'missing@example.com',
        'password' => 'wrong-password',
    ])->assertUnauthorized();
});

test('supports MCP initialization and tool discovery', function () {
    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'initialize',
    ])->assertOk()
        ->assertJsonPath('result.protocolVersion', '2025-06-18');

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 2,
        'method' => 'tools/list',
    ])->assertOk()
        ->assertJsonFragment(['name' => 'list_models'])
        ->assertJsonFragment(['name' => 'create_record']);
});

test('supports CRUD tool names as direct JSON-RPC methods', function () {
    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 20,
        'method' => 'list_models',
        'params' => [],
    ])->assertOk()
        ->assertJsonPath('result.structuredContent.models.0.name', 'academic_calendars');

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 21,
        'method' => 'describe_model',
        'params' => ['model' => 'academic_years'],
    ])->assertOk()
        ->assertJsonPath('result.structuredContent.name', 'academic_years');

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 22,
        'method' => 'list_records',
        'params' => ['model' => 'academic_years'],
    ])->assertOk()
        ->assertJsonStructure(['result' => ['structuredContent' => ['data', 'pagination']]]);

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 23,
        'method' => 'get_record',
        'params' => ['model' => 'academic_years', 'id' => '01missing'],
    ])->assertOk()
        ->assertJsonPath('error.code', -32602);
});

test('supports direct create update and delete methods', function () {
    config(['mcp.allow_mutations' => true]);

    $create = mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 24,
        'method' => 'create_record',
        'params' => [
            'model' => 'academic_years',
            'data' => [
                'year' => '2026/2027',
                'semester' => 'ganjil',
                'headmaster_name' => 'Direct MCP Test',
                'headmaster_nip' => '123456789',
                'date_start' => '2026-07-01',
                'date_end' => '2026-12-31',
                'active' => false,
            ],
        ],
    ])->assertOk();

    $id = $create->json('result.structuredContent.id');

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 25,
        'method' => 'update_record',
        'params' => [
            'model' => 'academic_years',
            'id' => $id,
            'data' => ['year' => '2026/2027 updated'],
        ],
    ])->assertOk()
        ->assertJsonPath('result.structuredContent.year', '2026/2027 updated');

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 26,
        'method' => 'delete_record',
        'params' => ['model' => 'academic_years', 'id' => $id],
    ])->assertOk()
        ->assertJsonPath('result.structuredContent.deleted', true);
});

test('does not permit mutations unless explicitly enabled', function () {
    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 3,
        'method' => 'tools/call',
        'params' => [
            'name' => 'create_record',
            'arguments' => [
                'model' => 'academic_years',
                'data' => ['year' => '2026'],
            ],
        ],
    ])->assertOk()
        ->assertJsonPath('error.code', -32602);
});

test('supports CRUD operations for an allowlisted model', function () {
    config(['mcp.allow_mutations' => true]);

    $create = mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 4,
        'method' => 'tools/call',
        'params' => [
            'name' => 'create_record',
            'arguments' => [
                'model' => 'academic_years',
                'data' => [
                    'year' => '2026/2027',
                    'semester' => 'ganjil',
                    'headmaster_name' => 'Test Headmaster',
                    'headmaster_nip' => '123456789',
                    'date_start' => '2026-07-01',
                    'date_end' => '2026-12-31',
                    'active' => false,
                ],
            ],
        ],
    ])->assertOk();

    $id = $create->json('result.structuredContent.id');

    expect($id)->toBeString();

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 5,
        'method' => 'tools/call',
        'params' => [
            'name' => 'update_record',
            'arguments' => [
                'model' => 'academic_years',
                'id' => $id,
                'data' => ['year' => '2026/2027 updated'],
            ],
        ],
    ])->assertOk()
        ->assertJsonPath('result.structuredContent.year', '2026/2027 updated');

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 6,
        'method' => 'tools/call',
        'params' => [
            'name' => 'get_record',
            'arguments' => ['model' => 'academic_years', 'id' => $id],
        ],
    ])->assertOk()
        ->assertJsonPath('result.structuredContent.id', $id);

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 7,
        'method' => 'tools/call',
        'params' => [
            'name' => 'delete_record',
            'arguments' => ['model' => 'academic_years', 'id' => $id],
        ],
    ])->assertOk()
        ->assertJsonPath('result.structuredContent.deleted', true);
});

test('validates journal target relationships before creating a record', function () {
    config(['mcp.allow_mutations' => true]);

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 8,
        'method' => 'tools/call',
        'params' => [
            'name' => 'create_record',
            'arguments' => [
                'model' => 'journals',
                'data' => [
                    'academic_year_id' => '01invalidacademicyear',
                    'subject_id' => '01invalidsubject',
                    'grade_id' => '01invalidgrade',
                    'user_id' => '01invaliduser',
                    'date' => '2026-09-17',
                    'main_target_id' => ['01missingmaintarget'],
                    'target_id' => ['01missingtarget'],
                    'chapter' => 'Test',
                    'activity' => 'Test activity',
                    'notes' => null,
                    'status' => 'Belum Dilaksanakan',
                ],
            ],
        ],
    ])->assertOk()
        ->assertJsonPath('error.code', -32602)
        ->assertJsonPath('error.message', 'One or more main_target_id values do not exist.');
});

// Additional generic relation validation tests

test('validates target main_target_id before creating a target', function () {
    config(['mcp.allow_mutations' => true]);

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 9,
        'method' => 'tools/call',
        'params' => [
            'name' => 'create_record',
            'arguments' => [
                'model' => 'targets',
                'data' => [
                    'user_id' => '01invaliduser',
                    'subject_id' => '01invalidsubject',
                    'grade_id' => '01invalidgrade',
                    'academic_year_id' => '01invalidyear',
                    'main_target_id' => '01missingmaintarget',
                    'target' => 'Some target',
                ],
            ],
        ],
    ])->assertOk()
      ->assertJsonPath('error.code', -32602)
      ->assertJsonPath('error.message', 'main_target_id value does not exist.');
});

test('validates attendance student_id before creating attendance', function () {
    config(['mcp.allow_mutations' => true]);

    mcpRequest([
        'jsonrpc' => '2.0',
        'id' => 10,
        'method' => 'tools/call',
        'params' => [
            'name' => 'create_record',
            'arguments' => [
                'model' => 'attendances',
                'data' => [
                    'student_id' => '01missingstudent',
                    'date' => '2026-09-17',
                    'status' => 'present',
                ],
            ],
        ],
    ])->assertOk()
      ->assertJsonPath('error.code', -32602)
      ->assertJsonPath('error.message', 'student_id value does not exist.');
});
