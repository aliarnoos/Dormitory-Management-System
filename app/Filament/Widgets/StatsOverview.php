<?php

namespace App\Filament\Widgets;

use App\Models\Inspection;
use App\Models\Reservation;
use App\Models\Room;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Filament\Facades\Filament;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Reservations',
                Reservation::where('status', 'pending')->count()
            )->description('Number of pending reservations')
            ->descriptionIcon('heroicon-m-clock')
            ->color('warning'),
            Stat::make(
                'Inspections',
                Inspection::where('status', 'pending')->count()
            )->description('Number of pending inspections')
            ->descriptionIcon('heroicon-m-clock')
            ->color('info'),
            Stat::make(
                'Students',
                User::where('role', 'student')->count()
            )->description('Number of students')
            ->descriptionIcon('heroicon-m-academic-cap')
            ->color('success'),
            Stat::make(
                'Available Rooms',
                Room::where('is_available', true)->count()
            )->description('Number of available rooms')
            ->descriptionIcon('heroicon-m-home')
            ->color('primary'),
        ];
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()->role === 'fmd';
    }
}
