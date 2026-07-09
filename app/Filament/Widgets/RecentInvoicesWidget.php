¿<?php
namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInvoicesWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Invoices';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Invoice::with('user')->latest())
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Invoice')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('formatted_total')
                    ->label('Total')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('due_at')
                    ->dateTime(),
            ])
            ->paginated([5]);
    }
}
