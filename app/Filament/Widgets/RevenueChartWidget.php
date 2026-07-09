<?php
namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Revenue';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data->push([
                'date' => $date->format('M Y'),
                'revenue' => Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total'),
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->pluck('revenue')->toArray(),
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
