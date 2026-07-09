¿<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Orders & Billing';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Services';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Service Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('id'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Customer'),
                        Infolists\Components\TextEntry::make('product.name')
                            ->label('Product'),
                        Infolists\Components\TextEntry::make('plan.name')
                            ->label('Plan'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                'suspended' => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('formatted_price')
                            ->label('Price'),
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Expires At')
                            ->dateTime()
                            ->placeholder('Never'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Service')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => "#{$state}"),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        'suspended' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('formatted_price')
                    ->label('Price')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'cancelled' => 'Cancelled',
                        'suspended' => 'Suspended',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Suspend Service')
                    ->modalDescription('This will suspend the service on Pterodactyl.')
                    ->action(fn (Service $record) => static::suspendService($record))
                    ->visible(fn (Service $record): bool => $record->status === 'active'),
                Tables\Actions\Action::make('unsuspend')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Unsuspend Service')
                    ->modalDescription('This will unsuspend the service on Pterodactyl.')
                    ->action(fn (Service $record) => static::unsuspendService($record))
                    ->visible(fn (Service $record): bool => $record->status === 'suspended'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function suspendService(Service $record): void
    {
        // TODO: Implement Pterodactyl suspend
        $record->update(['status' => 'suspended']);
        Notification::make()->title('Service suspended')->success()->send();
    }

    public static function unsuspendService(Service $record): void
    {
        // TODO: Implement Pterodactyl unsuspend
        $record->update(['status' => 'active']);
        Notification::make()->title('Service unsuspended')->success()->send();
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
            'index' => Pages\ListServices::route('/'),
            'view' => Pages\ViewService::route('/{record}'),
        ];
    }
}
