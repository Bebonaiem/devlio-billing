<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Active Services', Service::where('status', 'active')->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-o-server-stack')
                ->color('success'),
            Stat::make('Total Revenue', Order::sum('total'))
                ->description('All time revenue')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),
            Stat::make('Pending Invoices', Invoice::where('status', 'pending')->count())
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('danger'),
        ];
    }
}
