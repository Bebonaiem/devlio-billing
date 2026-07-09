<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                Plan::TYPE_FREE => 'Free',
                                Plan::TYPE_ONE_TIME => 'One Time',
                                Plan::TYPE_RECURRING => 'Recurring',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('billing_period')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\Select::make('billing_unit')
                            ->options([
                                'day' => 'Day(s)',
                                'week' => 'Week(s)',
                                'month' => 'Month(s)',
                                'year' => 'Year(s)',
                            ])
                            ->default('month')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),
                Forms\Components\Section::make('Server Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('memory')
                            ->numeric()
                            ->suffix('MB'),
                        Forms\Components\TextInput::make('cpu')
                            ->numeric()
                            ->suffix('%'),
                        Forms\Components\TextInput::make('disk')
                            ->numeric()
                            ->suffix('MB'),
                        Forms\Components\TextInput::make('swap')
                            ->numeric()
                            ->suffix('MB')
                            ->default(0),
                        Forms\Components\TextInput::make('databases')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('backups')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('allocations')
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('nest_id')
                            ->numeric(),
                        Forms\Components\TextInput::make('egg_id')
                            ->numeric(),
                    ])->columns(3),
                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\Repeater::make('prices')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('currency_code')
                                    ->relationship('currency', 'code')
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                                Forms\Components\TextInput::make('setup_fee')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'success',
                        'one-time' => 'warning',
                        'recurring' => 'primary',
                    }),
                Tables\Columns\TextColumn::make('billing_period')
                    ->label('Billing')
                    ->formatStateUsing(fn ($state, $record): string => match ($record->type) {
                        'free' => 'Free',
                        'one-time' => 'One Time',
                        default => "{$state} {$record->billing_unit}(s)",
                    }),
                Tables\Columns\TextColumn::make('prices')
                    ->label('Price')
                    ->formatStateUsing(fn ($prices): string => $prices->pluck('price')->first() ?? '0.00')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('services_count')
                    ->counts('services')
                    ->label('Services')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        Plan::TYPE_FREE => 'Free',
                        Plan::TYPE_ONE_TIME => 'One Time',
                        Plan::TYPE_RECURRING => 'Recurring',
                    ]),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'view' => Pages\ViewPlan::route('/{record}'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
