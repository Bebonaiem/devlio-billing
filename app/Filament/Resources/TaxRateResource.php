<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TaxRateResource\Pages;
use App\Models\TaxRate;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxRateResource extends Resource
{
    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Settings';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-percent';
    }

    protected static ?string $model = TaxRate::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tax Rates';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Schemas\Components\Section::make('Tax Rate Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rate')
                            ->numeric()
                            ->required()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('country')
                            ->maxLength(2)
                            ->helperText('ISO 3166-1 alpha-2 country code'),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(255)
                            ->helperText('Leave empty for country-wide tax'),
                        Forms\Components\TextInput::make('zip')
                            ->maxLength(255)
                            ->helperText('Leave empty for state-wide tax'),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->placeholder('All'),
                Tables\Columns\TextColumn::make('zip')
                    ->placeholder('All'),
                Tables\Columns\TextColumn::make('city')
                    ->placeholder('All'),
            ])
            ->filters([
                Tables\Filters\Filter::make('country')
                    ->form([
                        Forms\Components\TextInput::make('country')
                            ->maxLength(2),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when($data['country'], fn ($q, $country) => $q->where('country', $country))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTaxRates::route('/'),
            'create' => Pages\CreateTaxRate::route('/create'),
            'view' => Pages\ViewTaxRate::route('/{record}'),
            'edit' => Pages\EditTaxRate::route('/{record}/edit'),
        ];
    }
}
