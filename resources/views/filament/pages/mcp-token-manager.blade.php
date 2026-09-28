<div class="space-y-4">
    <h2 class="text-xl font-bold">Token MCP</h2>
    <p class="text-sm text-gray-500">Untuk koneksi MCP (AI assistant). Dikelola via Sanctum.</p>

    <form wire:submit="createToken" class="flex gap-2">
        <input
            type="text"
            wire:model="newTokenName"
            placeholder="Nama token MCP"
            class="border rounded px-3 py-2 flex-1"
        />
        <input
            type="number"
            wire:model="newTokenTtl"
            placeholder="Masa aktif (menit)"
            class="border rounded px-3 py-2 w-32"
        />
        <button
            type="submit"
            class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700"
        >
            Buat Token
        </button>
    </form>

    @if ($selectedToken)
        <div class="bg-yellow-50 border border-yellow-300 rounded p-4">
            <p class="font-bold text-yellow-800">⚠️ Simpan token ini — tidak akan ditampilkan lagi:</p>
            <code class="block mt-1 p-2 bg-white rounded text-sm break-all select-all">
                {{ $selectedToken }}
            </code>
        </div>
    @endif

    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b">
                <th class="text-left py-2 px-3">ID</th>
                <th class="text-left py-2 px-3">Nama</th>
                <th class="text-left py-2 px-3">Kadaluarsa</th>
                <th class="text-left py-2 px-3">Terakhir Dipakai</th>
                <th class="text-left py-2 px-3">Dibuat</th>
                <th class="py-2 px-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->getTableRecords() as $token)
                <tr class="border-b">
                    <td class="py-2 px-3 font-mono text-xs">{{ \Illuminate\Support\Str::limit($token->id, 12) }}</td>
                    <td class="py-2 px-3">{{ $token->name ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $token->expires_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $token->last_used_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $token->created_at->format('d M Y') }}</td>
                    <td class="py-2 px-3">
                        <button
                            wire:click="deleteToken({{ $token->id }})"
                            class="text-red-600 hover:underline text-sm"
                        >
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-gray-400">Belum ada MCP token</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
