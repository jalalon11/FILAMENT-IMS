<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class BulkTransaction extends Page implements HasForms
{
    use InteractsWithForms;

    // Hidden from sidebar - accessed via Transaction list page button
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'New Transaction';

    protected static string $view = 'filament.pages.bulk-transaction';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'type' => 'out',
            'transaction_date' => now()->toDateString(),
            'items' => [
                ['product_id' => null, 'quantity' => 1, 'unit_price' => null, 'total' => null],
            ],
        ]);
    }

    public function form(Form $form): Form
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
                            ->default('out')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // Update all existing items' prices when type changes
                                $items = $get('items') ?? [];
                                $updatedItems = [];
                                $hasProducts = false;

                                foreach ($items as $key => $item) {
                                    if (!empty($item['product_id'])) {
                                        $hasProducts = true;
                                        $product = Product::find($item['product_id']);
                                        if ($product) {
                                            $newPrice = $state === 'out' ? $product->selling_price : $product->cost_price;
                                            $item['unit_price'] = $newPrice;
                                            $qty = (float) ($item['quantity'] ?? 1);
                                            $item['total'] = number_format($qty * $newPrice, 2, '.', '');
                                        }
                                    }
                                    $updatedItems[$key] = $item;
                                }

                                $set('items', $updatedItems);

                                // Show notification about price update
                                if ($hasProducts) {
                                    $typeLabel = $state === 'out' ? 'Selling Price' : 'Cost Price';
                                    \Filament\Notifications\Notification::make()
                                        ->title('Prices Updated')
                                        ->body("Product prices changed to {$typeLabel} based on transaction type.")
                                        ->info()
                                        ->duration(3000)
                                        ->send();
                                }
                            }),
                        Forms\Components\DatePicker::make('transaction_date')
                            ->default(now())
                            ->required()
                            ->displayFormat('M d, Y'),
                        Forms\Components\TextInput::make('reference_number')
                            ->placeholder('Receipt No., Invoice No., PO No., etc.')
                            ->maxLength(100),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Products')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->pluck('product_name', 'id'))
                                    ->getOptionLabelFromRecordUsing(fn(Product $record) => "{$record->product_code} - {$record->product_name}")
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $type = $get('../../type');
                                                $set('unit_price', $type === 'out' ? $product->selling_price : $product->cost_price);
                                                $set('available_stock', $product->quantity);
                                                $set('unit_of_measure', $product->unit_of_measure);
                                                // Calculate total
                                                $qty = (float) ($get('quantity') ?? 1);
                                                $set('total', number_format($qty * ($type === 'out' ? $product->selling_price : $product->cost_price), 2, '.', ''));
                                            }
                                        }
                                    })
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $qty = (float) ($get('quantity') ?? 0);
                                        $price = (float) ($get('unit_price') ?? 0);
                                        $set('total', number_format($qty * $price, 2, '.', ''));
                                    })
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, $fail) use ($get) {
                                                $type = $get('../../type');
                                                if ($type === 'out') {
                                                    $productId = $get('product_id');
                                                    if ($productId) {
                                                        $product = Product::find($productId);
                                                        if ($product && $value > $product->quantity) {
                                                            $fail("Insufficient stock! Available: {$product->quantity} {$product->unit_of_measure}");
                                                        }
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->helperText(function (Get $get) {
                                        $productId = $get('product_id');
                                        $quantity = (int) ($get('quantity') ?? 1);
                                        $type = $get('../../type');

                                        if ($productId) {
                                            $product = Product::find($productId);
                                            if ($product) {
                                                $currentStock = $product->quantity;
                                                $unit = $product->unit_of_measure;

                                                if ($currentStock <= 0 && $type === 'out') {
                                                    return "⚠️ OUT OF STOCK! Current: 0 {$unit}";
                                                }

                                                if ($type === 'out') {
                                                    $newStock = $currentStock - $quantity;
                                                    $status = $newStock < 0 ? " ⚠️ INSUFFICIENT!" : "";
                                                    return "Stock: {$currentStock} → {$newStock} {$unit}{$status}";
                                                } else {
                                                    $newStock = $currentStock + $quantity;
                                                    return "Stock: {$currentStock} → {$newStock} {$unit}";
                                                }
                                            }
                                        }
                                        return null;
                                    })
                                    ->columnSpan(1),
                                // Forms\Components\TextInput::make('unit_of_measure')
                                //     ->label('Unit')
                                //     ->disabled()
                                //     ->dehydrated(false)
                                //     ->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required()
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $qty = (float) ($get('quantity') ?? 0);
                                        $price = (float) ($get('unit_price') ?? 0);
                                        $set('total', number_format($qty * $price, 2, '.', ''));
                                    })
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->prefix('₱')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(2),
                                Forms\Components\Hidden::make('available_stock'),
                            ])
                            ->columns(9)
                            ->addActionLabel('Add Product')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['product_id'])
                                ? Product::find($state['product_id'])?->product_name . ' × ' . ($state['quantity'] ?? 1)
                                : 'New Item'
                            )
                            ->minItems(1)
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->placeholder('Additional notes or remarks')
                            ->rows(2),
                    ])
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Validate stock for out transactions
        if ($data['type'] === 'out') {
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $item['quantity'] > $product->quantity) {
                    Notification::make()
                        ->title('Insufficient Stock')
                        ->body("Not enough stock for {$product->product_name}. Available: {$product->quantity}")
                        ->danger()
                        ->send();
                    return;
                }
            }
        }

        try {
            DB::beginTransaction();

            foreach ($data['items'] as $item) {
                Transaction::create([
                    'product_id' => $item['product_id'],
                    'type' => $data['type'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $item['quantity'] * $item['unit_price'],
                    'reference_number' => $data['reference_number'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'transaction_date' => $data['transaction_date'],
                ]);
            }

            DB::commit();

            $type = $data['type'] === 'in' ? 'received' : 'released';
            $count = count($data['items']);

            Notification::make()
                ->title('Transactions Saved!')
                ->body("{$count} product(s) successfully {$type}.")
                ->success()
                ->send();

            // Reset form
            $this->form->fill([
                'type' => $data['type'],
                'transaction_date' => now()->toDateString(),
                'items' => [],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error')
                ->body('Failed to save transactions: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getGrandTotal(): float
    {
        $items = $this->data['items'] ?? [];
        return collect($items)->sum(fn($item) => (float) ($item['total'] ?? 0));
    }
}
