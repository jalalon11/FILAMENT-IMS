<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\TextInput::make('product_code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Enter product code (e.g., PRD-001)')
                            ->helperText('Unique identifier for this product'),
                        Forms\Components\TextInput::make('product_name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter product name'),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(500),
                            ]),
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Company/Supplier')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(500),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing & Stock')
                    ->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->helperText('Current stock quantity'),
                        Forms\Components\Select::make('unit_of_measure')
                            ->options([
                                'pcs' => 'Pieces (pcs)',
                                'box' => 'Box',
                                'kg' => 'Kilogram (kg)',
                                'g' => 'Gram (g)',
                                'L' => 'Liter (L)',
                                'mL' => 'Milliliter (mL)',
                                'pack' => 'Pack',
                                'set' => 'Set',
                                'unit' => 'Unit',
                                'ream' => 'Ream',
                                'roll' => 'Roll',
                                'case' => 'Case',
                            ])
                            ->default('pcs')
                            ->required()
                            ->native(false)
                            ->searchable(),
                        Forms\Components\TextInput::make('cost_price')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->helperText('Purchase/cost price'),
                        Forms\Components\TextInput::make('selling_price')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->helperText('Selling price to customers'),
                        Forms\Components\DatePicker::make('date_received')
                            ->default(now())
                            ->displayFormat('M d, Y')
                            ->helperText('Date the product was received'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Product code copied'),
                Tables\Columns\TextColumn::make('product_name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable()
                    ->label('Company'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) => $state . ' ' . $record->unit_of_measure)
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('cost_price')
                    ->money('PHP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('selling_price')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('profit_margin')
                    ->money('PHP')
                    ->label('Profit/Unit')
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_received')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn(Builder $query): Builder => $query->where('quantity', '<=', 10))
                    ->label('Low Stock (≤10)'),
                Tables\Filters\Filter::make('out_of_stock')
                    ->query(fn(Builder $query): Builder => $query->where('quantity', '<=', 0))
                    ->label('Out of Stock'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->modalWidth('7xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('7xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Create your first product to start managing inventory.')
            ->emptyStateIcon('heroicon-o-cube')
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProducts::route('/'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }
}
