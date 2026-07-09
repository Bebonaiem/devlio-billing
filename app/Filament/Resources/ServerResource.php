<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ServerResource\Pages;
use App\Models\Server;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;


use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServerResource extends Resource
{
    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Servers';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-server-stack';
    }

    protected static ?string $model = Server::class;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
                Schemas\Components\Section::make('Server Details')
                    ->schema([
                        Schemas\Components\TextEntry::make('id'),
                        Schemas\Components\TextEntry::make('user.email')
                            ->label('Owner'),
                        Schemas\Components\TextEntry::make('service.product.name')
                            ->label('Product'),
                        Schemas\Components\TextEntry::make('external_id')
                            ->label('Pterodactyl ID'),
                        Schemas\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'suspended' => 'danger',
                                'pending' => 'warning',
                            }),
                        Schemas\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Server')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => "#{$state}"),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('external_id')
                    ->label('Pterodactyl ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        'pending' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'pending' => 'Pending',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Suspend Server')
                    ->modalDescription('This will suspend the server on Pterodactyl.')
                    ->action(fn (Server $record) => static::suspendServer($record))
                    ->visible(fn (Server $record): bool => $record->status === 'active'),
                Tables\Actions\Action::make('unsuspend')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Unsuspend Server')
                    ->modalDescription('This will unsuspend the server on Pterodactyl.')
                    ->action(fn (Server $record) => static::unsuspendServer($record))
                    ->visible(fn (Server $record): bool => $record->status === 'suspended'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function suspendServer(Server $record): void
    {
        // TODO: Implement Pterodactyl suspend
        $record->update(['status' => 'suspended']);
        Notification::make()->title('Server suspended')->success()->send();
    }

    public static function unsuspendServer(Server $record): void
    {
        // TODO: Implement Pterodactyl unsuspend
        $record->update(['status' => 'active']);
        Notification::make()->title('Server unsuspended')->success()->send();
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
            'index' => Pages\ListServers::route('/'),
            'view' => Pages\ViewServer::route('/{record}'),
        ];
    }
}
