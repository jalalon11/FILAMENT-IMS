<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class TransactionHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Transaction History';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.transaction-history';

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->with(['product.category', 'product.company']))
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date('M d, Y')
                    ->sortable()
                    ->label('Date'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('h:i A')
                    ->label('Time')
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
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_name')
                    ->label('Product')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('product.category.name')
                    ->label('Category')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('product.company.name')
                    ->label('Company')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->suffix(fn($record) => ' ' . ($record->product->unit_of_measure ?? 'pcs')),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('PHP')
                    ->label('Unit Price'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PHP')
                    ->label('Total')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                    ])
                    ->label('Transaction Type'),
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'product_name')
                    ->searchable()
                    ->preload()
                    ->label('Product'),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('product.category', 'name')
                    ->preload()
                    ->label('Category'),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn(Builder $q, $date) => $q->whereDate('transaction_date', '>=', $date))
                            ->when($data['until'], fn(Builder $q, $date) => $q->whereDate('transaction_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Transaction Details'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('No transactions yet')
            ->emptyStateDescription('Transaction history will appear here once you record stock movements.');
    }
}
