<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Filament\Facades\Filament;

class DepositsOverview extends ChartWidget
{
    protected static ?string $heading = 'Deposits Count';

    protected function getData(): array
    {
        return [
            'labels' => [
                'Has Deposit',
                'No Deposit',
            ],
            'datasets' => [
                [
                    'data' => [User::where('role', 'student')->where('has_deposit', true)->count(), User::where('role', 'student')->where('has_deposit', false)->count()],
                    'backgroundColor' => ['#2086e6', '#ccd1eb'],
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->role === 'finance';
    }
}
