<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;

class ReservationStatusBar extends ChartWidget
{
    protected static ?string $heading = 'Reservation Status';

    protected function getData(): array
    {
        return [
            'labels' => [
                'Pending',
                'Completed',
                'Cancelled',
            ],
            'datasets' => [
                [
                    'data' => [Reservation::where('status', 'pending')->count(), Reservation::where('status', 'completed')->count(), Reservation::where('status', 'cancelled')->count()],
                    'backgroundColor' => ['#c4c4c4', '#4ebf00', '#a83232'],
                    'borderColor' => ['#ffffff'],
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
