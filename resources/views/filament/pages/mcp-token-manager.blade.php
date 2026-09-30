<x-filament::section
    heading="Token MCP"
    description="Kelola token untuk menghubungkan MCP dengan aplikasi atau asisten AI."
>
    <div class="space-y-6">
        @php($tokens = $this->getMcpTokens())

        @if ($selectedToken)
            <div class="space-y-3 rounded-xl border border-warning-300 bg-warning-50 p-4 dark:border-warning-400/30 dark:bg-warning-400/10">
                <div>
                    <p class="font-semibold text-warning-800 dark:text-warning-200">Token berhasil dibuat</p>
                    <p class="mt-1 text-sm text-warning-700 dark:text-warning-300">
                        Salin dan simpan sekarang. Demi keamanan, token lengkap tidak akan ditampilkan lagi.
                    </p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input
                        type="text"
                        readonly
                        value="{{ $selectedToken }}"
                        aria-label="Token MCP yang baru dibuat"
                        class="min-w-0 flex-1 rounded-lg border-gray-300 bg-white font-mono text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900"
                        onfocus="this.select()"
                    />
                    <x-filament::button
                        color="gray"
                        type="button"
                        wire:click="$set('selectedToken', null)"
                    >
                        Selesai
                    </x-filament::button>
                </div>
            </div>
        @endif

        <form wire:submit="createToken" class="space-y-4">
            <div class="max-w-xl space-y-1.5">
                <label for="new-token-name" class="text-sm font-medium text-gray-950 dark:text-white">
                    Nama token
                </label>
                <input
                    id="new-token-name"
                    type="text"
                    wire:model="newTokenName"
                    placeholder="Contoh: asisten-guru"
                    maxlength="255"
                    required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                />
                @error('newTokenName')
                    <p class="text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                @enderror
            </div>

            <x-filament::button type="submit" icon="heroicon-o-key">
                Buat token
            </x-filament::button>
        </form>

        <div class="border-t border-gray-200 pt-5 dark:border-white/10">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Token aktif</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $tokens->count() }} token
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
                <table class="w-full divide-y divide-gray-200 text-left text-sm dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Nama</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Kadaluarsa</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Terakhir dipakai</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Dibuat</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse ($tokens as $token)
                            <tr wire:key="mcp-token-{{ $token->id }}">
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-950 dark:text-white">
                                    {{ $token->name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $token->expires_at?->format('d M Y H:i') ?? 'Tidak ada batas' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $token->last_used_at?->format('d M Y H:i') ?? 'Belum pernah' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $token->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <x-filament::button
                                        color="danger"
                                        size="sm"
                                        outlined
                                        type="button"
                                        wire:click="deleteToken({{ $token->id }})"
                                        wire:confirm="Yakin ingin menghapus token MCP ini?"
                                    >
                                        Hapus
                                    </x-filament::button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada token MCP. Buat token untuk mulai menghubungkan aplikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament::section>
