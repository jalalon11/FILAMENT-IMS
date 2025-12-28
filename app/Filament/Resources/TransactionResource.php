<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Stock Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\ToggleButtons::make('type')
                            ->options([
                                'in' => 'Stock In (Receiving)',
                                'out' => 'Stock Out (Selling)',
                            ])
                            ->icons([
                                'in' => 'heroicon-o-arrow-down-tray',
                                'out' => 'heroicon-o-arrow-up-tray',
                            ])
                            ->colors([
                                'in' => 'success',
                                'out' => 'danger',
                            ])
                            ->required()
                            ->inline()
                            ->default('in')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $productId = $get('product_id');
                                if ($productId) {
                                    $product = Product::find($productId);
                                    if ($product) {
                                        $type = $get('type');
                                        $set('unit_price', $type === 'out' ? $product->selling_price : $product->cost_price);
                                        static::updateTotalAmount($get, $set);
                                    }
                                }
                            }),
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'product_name')
                            ->getOptionLabelFromRecordUsing(fn(Product $record) => "{$record->product_code} - {$record->product_name}")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $type = $get('type');
                                        $set('unit_price', $type === 'out' ? $product->selling_price : $product->cost_price);
                                        static::updateTotalAmount($get, $set);
                                    }
                                }
                            })
                            ->helperText(function (Get $get) {
                                $productId = $get('product_id');
                                if ($productId) {
                                    $product = Product::find($productId);
                                    if ($product) {
                                        return "Current stock: {$product->quantity} units";
                                    }
                                }
                                return null;
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn(Get $get, Set $set) => static::updateTotalAmount($get, $set))
                            ->rules([
                                function (Get $get) {
                                    return function (string $attribute, $value, $fail) use ($get) {
                                        if ($get('type') === 'out') {
                                            $productId = $get('product_id');
                                            if ($productId) {
                                                $product = Product::find($productId);
                                                if ($product && $value > $product->quantity) {
                                                    $fail("Insufficient stock. Available: {$product->quantity} units.");
                                                }
                                            }
                                        }
                                    };
                                },
                            ]),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->minValue(0)
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn(Get $get, Set $set) => static::updateTotalAmount($get, $set)),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('₱')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Auto-calculated: Quantity × Unit Price'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->maxLength(100)
                            ->placeholder('e.g., Receipt No., PO No., etc.')
                            ->helperText('Optional reference for tracking'),
                        Forms\Components\DatePicker::make('transaction_date')
                            ->default(now())
                            ->required()
                            ->displayFormat('M d, Y'),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(500)
                            ->placeholder('Additional notes or remarks')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    protected static function updateTotalAmount(Get $get, Set $set): void
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $unitPrice = (float) ($get('unit_price') ?? 0);
        $set('total_amount', number_format($quantity * $unitPrice, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'STOCK IN',
                        'out' => 'STOCK OUT',
                        default => strtoupper($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'in' => 'heroicon-o-arrow-down-tray',
                        'out' => 'heroicon-o-arrow-up-tray',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                Tables\Columns\TextColumn::make('product.product_code')
                    ->label('Product Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_name')
                    ->label('Product')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('profit')
                    ->money('PHP')
                    ->color('success')
                    ->label('Profit')
                    ->visible(fn() => true)
                    ->getStateUsing(fn(Transaction $record) => $record->profit),
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                    ]),
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'product_name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No transactions yet')
            ->emptyStateDescription('Record your first stock in/out transaction.')
            ->emptyStateIcon('heroicon-o-arrows-right-left')
            ->defaultSort('transaction_date', 'desc');
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
