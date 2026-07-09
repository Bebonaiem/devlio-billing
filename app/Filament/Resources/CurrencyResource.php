<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Currencies';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Currency Details')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(3)
                            ->unique(ignoreRecord: true)
                            ->helperText('ISO 4217 currency code (e.g., USD, EUR)'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('prefix')
                            ->maxLength(10)
                            ->default('$'),
                        Forms\Components\TextInput::make('suffix')
                            ->maxLength(10)
                            ->default(''),
                        Forms\Components\Toggle::make('enabled')
                            ->default(true),
                        Forms\Components\Toggle::make('default')
                            ->default(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state) {
                                    Currency::where('id', '!=', $get('id'))
                                        ->where('default', true)
                                        ->update(['default' => false]);
                                }
                            }),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prefix'),
                Tables\Columns\TextColumn::make('suffix'),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
                Tables\Columns\IconColumn::make('default')
                    ->boolean()
                    ->label('Default'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('setDefault')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Set as Default')
                    ->modalDescription('This will set this currency as the default for all prices.')
                    ->action(function ($record) {
                        Currency::where('id', '!=', $record->id)
                            ->update(['default' => false]);
                        $record->update(['default' => true]);
                        Notification::make()->title('Currency set as default')->success()->send();
                    })
                    ->visible(fn ($record): bool => ! $record->default),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'view' => Pages\ViewCurrency::route('/{record}'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
