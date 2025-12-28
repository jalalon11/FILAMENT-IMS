<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactions extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Transactions';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->with('product')
                    ->latest('transaction_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'IN',
                        'out' => 'OUT',
                        default => strtoupper($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('product.product_name')
                    ->label('Product')
                    ->limit(30),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PHP')
                    ->weight('bold'),
            ])
            ->paginated(false);
    }
}
