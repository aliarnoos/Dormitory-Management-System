<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Facades\Filament;

class GenderStaus extends ChartWidget
{
    protected static ?string $heading = 'Students';
    protected function getData(): array
    {
        return [
            'labels' => [
                'Males',
                'Females',
            ],
            'datasets' => [
                [
                    'data' => [User::where('role', 'student')->where('gender', 'male')->count(), User::where('role', 'student')->where('gender', 'female')->count()],
                    'backgroundColor' => ['#36A2EB', '#FFC0CB'],
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->role === 'fmd';
    }
}
