<?php

namespace App\Filament\Widgets;

use App\Models\Bonsai;
use Filament\Widgets\ChartWidget;

class BonsaiSizeStats extends ChartWidget
{
    protected ?string $heading = 'Statistik Ukuran Bonsai';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Bonsai',
                    'data' => [
                        Bonsai::where('size', 'Small')->count(),
                        Bonsai::where('size', 'Medium')->count(),
                        Bonsai::where('size', 'Large')->count(),
                        Bonsai::where('size', 'Mame')->count(),
                        Bonsai::where('size', 'Shito')->count(),
                        Bonsai::where('size', 'Extra Large')->count(),
                    ],
                ],
            ],

            'labels' => [
                'Small',
                'Medium',
                'Large',
                'Mame',
                'Shito',
                'Extra Large',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
