<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hanya drop jika tabel masih ada (production sudah mungkin dialihkan ke Sanctum)
        Schema::dropIfExists('mcp_tokens');
    }

    public function down(): void
    {
        // Tidak bisa restore — data sudah dipindah ke personal_access_tokens
        Schema::create('mcp_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }
};
