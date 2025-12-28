<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Low Stock Alert';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('quantity', '<=', 10)
                    ->orderBy('quantity', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product_code')
                    ->label('Code')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->limit(20),
                Tables\Columns\TextColumn::make('quantity')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'info',
                    }),
            ])
            ->paginated(false)
            ->emptyStateHeading('All stocked!')
            ->emptyStateDescription('No products running low.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
