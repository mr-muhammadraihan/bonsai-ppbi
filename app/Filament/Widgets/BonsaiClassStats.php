<?php

namespace App\Filament\Widgets;

use App\Models\Bonsai;
use Filament\Widgets\ChartWidget;

class BonsaiClassStats extends ChartWidget
{
    protected ?string $heading = 'Statistik Kelas Bonsai';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Bonsai',
                    'data' => [
                        Bonsai::where('class', 'Jadi')->count(),
                        Bonsai::where('class', 'Prospek')->count(),
                    ],
                ],
            ],

            'labels' => [
                'Jadi',
                'Prospek',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
