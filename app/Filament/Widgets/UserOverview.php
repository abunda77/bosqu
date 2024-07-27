<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Property;
use App\Filament\Resources\UserResource;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Unique views', '192.1k')
            ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),

            Stat::make(label:'Total Members', value:User::count())
                ->descriptionIcon('heroicon-s-user')
                ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

                Stat::make(label:'Total Asset Properti', value:Property::count())
                ->descriptionIcon('heroicon-s-home')
                ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
        ];
    }
}
