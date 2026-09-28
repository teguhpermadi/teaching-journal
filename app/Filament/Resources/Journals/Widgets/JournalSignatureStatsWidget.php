<?php

namespace App\Filament\Resources\Journals\Widgets;

use App\Models\Journal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class JournalSignatureStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        $myJournals = Journal::where('user_id', $user->id);

        $totalJournals = $myJournals->count();

        $signedByOwner = Journal::where('user_id', $user->id)
            ->whereHas('signatures', function ($query) {
                $query->where('signer_role', 'owner');
            })
            ->count();

        $unsignedByOwner = $totalJournals - $signedByOwner;

        $signedByHeadmaster = Journal::where('user_id', $user->id)
            ->whereHas('signatures', function ($query) {
                $query->where('signer_role', 'headmaster');
            })
            ->count();

        $unsignedByHeadmaster = $totalJournals - $signedByHeadmaster;

        return [
            Stat::make('Total Jurnal', $totalJournals)
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Belum Ditandatangani Guru', $unsignedByOwner)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Sudah Ditandatangani Guru', $signedByOwner)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Belum Ditandatangani Kepala Sekolah', $unsignedByHeadmaster)
                ->icon('heroicon-o-x-circle')
                ->color('warning'),

            Stat::make('Sudah Ditandatangani Kepala Sekolah', $signedByHeadmaster)
                ->icon('heroicon-o-check-circle')
                ->color('info'),
        ];
    }
}
