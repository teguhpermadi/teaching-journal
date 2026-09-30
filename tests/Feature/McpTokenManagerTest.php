<?php

use App\Filament\Resources\Users\Pages\McpTokenManager;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate([
        'name' => 'teacher',
        'guard_name' => 'web',
    ]);
});

test('profile component creates lists and deletes the current users MCP tokens', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $issuedAt = now();
    $this->travelTo($issuedAt);

    $component = Livewire::test(McpTokenManager::class)
        ->set('newTokenName', 'desktop-client')
        ->call('createToken')
        ->assertHasNoErrors()
        ->assertSee('desktop-client');

    $token = PersonalAccessToken::query()
        ->where('tokenable_id', $user->getKey())
        ->where('tokenable_type', $user->getMorphClass())
        ->where('type', 'mcp')
        ->firstOrFail();

    expect($token->name)->toBe('desktop-client')
        ->and($token->abilities)->toBe(['mcp'])
        ->and($token->expires_at->equalTo($issuedAt->copy()->addDays(30)))->toBeTrue();

    $component->call('deleteToken', $token->getKey())
        ->assertDontSee('desktop-client');

    expect(PersonalAccessToken::query()->find($token->getKey()))->toBeNull();
});
