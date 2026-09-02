<?php

namespace App\Filament\Widgets;

use App\Models\Bonsai;
use App\Models\Participant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total Peserta',
                Participant::count()
            ),

            Stat::make(
                'Total Bonsai',
                Bonsai::count()
            ),

            Stat::make(
                'Pemenang',
                Bonsai::where('status', 'Pemenang')->count()
            ),
        ];
    }
}
