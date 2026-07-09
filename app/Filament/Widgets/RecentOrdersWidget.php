¿<?php
namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Orders';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::with('user')->latest())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order')
                    ->formatStateUsing(fn ($state): string => "#{$state}"),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('formatted_total')
                    ->label('Total')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->paginated([5]);
    }
}
