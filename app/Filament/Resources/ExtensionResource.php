<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ExtensionResource\Pages;
use App\Models\Extension;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExtensionResource extends Resource
{
    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Servers';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-puzzle-piece';
    }

    protected static ?string $model = Extension::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Extensions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Extension Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->disabled(),
                        Forms\Components\TextInput::make('version')
                            ->disabled(),
                        Forms\Components\Toggle::make('enabled')
                            ->label('Enabled'),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Extension Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'server' => 'primary',
                                'gateway' => 'success',
                            }),
                        Infolists\Components\TextEntry::make('version'),
                        Infolists\Components\IconColumn::make('enabled')
                            ->boolean(),
                    ])->columns(2),
                Infolists\Components\Section::make('Settings')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('settings')
                            ->schema([
                                Infolists\Components\TextEntry::make('key'),
                                Infolists\Components\TextEntry::make('value'),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'server' => 'primary',
                        'gateway' => 'success',
                    }),
                Tables\Columns\TextColumn::make('version'),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'server' => 'Server',
                        'gateway' => 'Gateway',
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
            'index' => Pages\ListExtensions::route('/'),
            'view' => Pages\ViewExtension::route('/{record}'),
            'edit' => Pages\EditExtension::route('/{record}/edit'),
        ];
    }
}
