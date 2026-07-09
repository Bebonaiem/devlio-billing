<?php
namespace App\Filament\Widgets;

use App\Models\Server;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServerStatusWidget extends BaseWidget
{
    protected ?string $heading = 'Server Status';

    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        return [
            Stat::make('Active Servers', Server::where('status', 'active')->count())
                ->description('Running')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Suspended Servers', Server::where('status', 'suspended')->count())
                ->description('Suspended')
                ->descriptionIcon('heroicon-o-no-symbol')
                ->color('danger'),
            Stat::make('Total Servers', Server::count())
                ->description('All servers')
                ->descriptionIcon('heroicon-o-server-stack')
                ->color('primary'),
        ];
    }
}
