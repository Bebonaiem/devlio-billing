¿<?php
namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Orders & Billing';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Invoices';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                Invoice::STATUS_PENDING => 'Pending',
                                Invoice::STATUS_PAID => 'Paid',
                                Invoice::STATUS_CANCELLED => 'Cancelled',
                                Invoice::STATUS_OVERDUE => 'Overdue',
                            ])
                            ->required(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Invoice Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('number')
                            ->label('Invoice Number')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Customer'),
                        Infolists\Components\TextEntry::make('formatted_total')
                            ->label('Total')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('formatted_remaining')
                            ->label('Remaining')
                            ->color(fn ($state): string => str_contains($state, '0.00') ? 'success' : 'warning'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'overdue' => 'danger',
                                'cancelled' => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('due_at')
                            ->label('Due At')
                            ->dateTime(),
                    ])->columns(3),
                Infolists\Components\Section::make('Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                Infolists\Components\TextEntry::make('description'),
                                Infolists\Components\TextEntry::make('quantity'),
                                Infolists\Components\TextEntry::make('price')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('total')
                                    ->state(fn ($record): float => $record->quantity * $record->price)
                                    ->money('USD'),
                            ])->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Invoice')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('formatted_total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('formatted_remaining')
                    ->label('Remaining')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Invoice::STATUS_PENDING => 'Pending',
                        Invoice::STATUS_PAID => 'Paid',
                        Invoice::STATUS_CANCELLED => 'Cancelled',
                        Invoice::STATUS_OVERDUE => 'Overdue',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
