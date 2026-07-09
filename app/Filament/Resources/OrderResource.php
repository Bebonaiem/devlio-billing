<?php
namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Schemas;
use Filament\Schemas\Schema;


use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Orders & Billing';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shopping-cart';
    }

    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
                Schemas\Components\Section::make('Order Details')
                    ->schema([
                        Schemas\Components\TextEntry::make('id'),
                        Schemas\Components\TextEntry::make('user.email')
                            ->label('Customer'),
                        Schemas\Components\TextEntry::make('formatted_total')
                            ->label('Total')
                            ->weight('bold'),
                        Schemas\Components\TextEntry::make('currency.code')
                            ->label('Currency'),
                        Schemas\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])->columns(2),
                Schemas\Components\Section::make('Services')
                    ->schema([
                        Schemas\Components\RepeatableEntry::make('services')
                            ->schema([
                                Schemas\Components\TextEntry::make('product.name'),
                                Schemas\Components\TextEntry::make('plan.name'),
                                Schemas\Components\TextEntry::make('formatted_price'),
                                Schemas\Components\TextEntry::make('status')
                                    ->badge(),
                            ])->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => "#{$state}"),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('formatted_total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('services_count')
                    ->counts('services')
                    ->label('Services'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
