<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Component;
use Laravel\Sanctum\PersonalAccessToken;

class McpTokenManager extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?string $newTokenName = '';
    public ?string $newTokenTtl = '43200';
    public ?string $selectedToken = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('newTokenName')
                    ->label('Nama Token')
                    ->placeholder('contoh: telegram-bot, whatsapp-api')
                    ->required()
                    ->maxLength(255),
                TextInput::make('newTokenTtl')
                    ->label('Masa Aktif (menit)')
                    ->numeric()
                    ->default(43200)
                    ->required(),
            ]);
    }

    public function createToken(): void
    {
        $this->validate([
            'newTokenName' => 'required|string|max:255',
            'newTokenTtl' => 'required|integer|min:1',
        ]);

        $user = Filament::auth()->user();
        $expiresAt = now()->addMinutes((int) $this->newTokenTtl);

        $newToken = $user->createToken(
            $this->newTokenName,
            ['mcp'],
            $expiresAt,
        );

        // Set type 'mcp' pada token Sanctum
        $newToken->accessToken->forceFill(['type' => 'mcp'])->save();
        $plainToken = $newToken->plainTextToken;

        $this->selectedToken = $plainToken;
        $this->newTokenName = '';
        $this->newTokenTtl = '43200';

        Notification::make()
            ->title('Token berhasil dibuat')
            ->body('Simpan token ini — tidak akan ditampilkan lagi.')
            ->success()
            ->send();
    }

    public function deleteToken(int $tokenId): void
    {
        $token = PersonalAccessToken::where('tokenable_id', Filament::auth()->id())
            ->where('tokenable_type', \App\Models\User::class)
            ->where('type', 'mcp')
            ->find($tokenId);

        if ($token) {
            $token->delete();
            Notification::make()
                ->title('Token dihapus')
                ->success()
                ->send();
        }
    }

    public function getTableQuery(): Builder
    {
        return PersonalAccessToken::query()
            ->where('tokenable_id', Filament::auth()->id())
            ->where('tokenable_type', \App\Models\User::class)
            ->where('type', 'mcp');
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->label('ID')
                ->limit(12),
            TextColumn::make('name')
                ->label('Nama')
                ->searchable(),
            TextColumn::make('expires_at')
                ->label('Kadaluarsa')
                ->dateTime('d M Y H:i')
                ->placeholder('-'),
            TextColumn::make('last_used_at')
                ->label('Terakhir Dipakai')
                ->dateTime('d M Y H:i')
                ->placeholder('-'),
            TextColumn::make('created_at')
                ->label('Dibuat')
                ->dateTime('d M Y')
                ->sortable(),
            Tables\Actions\DeleteAction::make()
                ->label('Hapus')
                ->requiresConfirmation()
                ->modalHeading('Hapus token?')
                ->action(fn (array $arguments) => $this->deleteToken($arguments['record'])),
        ];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.pages.mcp-token-manager');
    }
}
