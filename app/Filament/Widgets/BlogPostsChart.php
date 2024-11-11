<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class BlogPostsChart extends ChartWidget
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
                    'data' => [User::where('gender', 'm')->count(), User::where('gender', 'f')->count()],
                    'backgroundColor' => ['#36A2EB', '#FFC0CB'],
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
