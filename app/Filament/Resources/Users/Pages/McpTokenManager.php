<?php

namespace App\Filament\Resources\Users\Pages;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Laravel\Sanctum\PersonalAccessToken;

class McpTokenManager extends MyProfileComponent
{
    public string $newTokenName = '';
    public ?string $selectedToken = null;

    protected string $view = 'filament.pages.mcp-token-manager';

    public static $sort = 50;

    public function createToken(): void
    {
        $this->validate([
            'newTokenName' => ['required', 'string', 'max:255'],
        ]);

        $user = Filament::getCurrentOrDefaultPanel()->auth()->user();
        abort_unless($user instanceof User, 403);

        $expiresAt = now()->addDays(30);

        $newToken = $user->createToken(
            $this->newTokenName,
            ['mcp'],
            $expiresAt,
        );

        $newToken->accessToken->forceFill(['type' => 'mcp'])->save();
        $this->selectedToken = $newToken->plainTextToken;
        $this->reset('newTokenName');

        Notification::make()
            ->success()
            ->title('Token MCP berhasil dibuat')
            ->body('Simpan token ini sekarang. Token lengkap hanya ditampilkan sekali.')
            ->send();
    }

    public function deleteToken(int $tokenId): void
    {
        $user = Filament::getCurrentOrDefaultPanel()->auth()->user();
        abort_unless($user instanceof User, 403);

        $token = PersonalAccessToken::query()
            ->where('tokenable_id', $user->getKey())
            ->where('tokenable_type', $user->getMorphClass())
            ->where('type', 'mcp')
            ->findOrFail($tokenId);

        $token->delete();

        Notification::make()
            ->success()
            ->title('Token MCP dihapus')
            ->send();
    }

    public function getMcpTokens(): Collection
    {
        $user = Filament::getCurrentOrDefaultPanel()->auth()->user();
        abort_unless($user instanceof User, 403);

        return PersonalAccessToken::query()
            ->where('tokenable_id', $user->getKey())
            ->where('tokenable_type', $user->getMorphClass())
            ->where('type', 'mcp')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
