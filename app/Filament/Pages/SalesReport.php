<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SalesReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Sales Report';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.sales-report';

    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?string $preset = 'this_month';

    public function mount(): void
    {
        $this->applyPreset('this_month');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Date Range Filter')
                    ->schema([
                        Forms\Components\Select::make('preset')
                            ->options([
                                'today' => 'Today',
                                'yesterday' => 'Yesterday',
                                'this_week' => 'This Week',
                                'last_week' => 'Last Week',
                                'this_month' => 'This Month',
                                'last_month' => 'Last Month',
                                'this_year' => 'This Year',
                                'custom' => 'Custom Range',
                            ])
                            ->default('this_month')
                            ->live()
                            ->afterStateUpdated(fn($state) => $this->applyPreset($state)),
                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date')
                            ->displayFormat('M d, Y')
                            ->visible(fn() => $this->preset === 'custom'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('To Date')
                            ->displayFormat('M d, Y')
                            ->visible(fn() => $this->preset === 'custom'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function applyPreset(string $preset): void
    {
        $this->preset = $preset;

        $today = Carbon::today();

        match ($preset) {
            'today' => [$this->date_from, $this->date_to] = [$today->toDateString(), $today->toDateString()],
            'yesterday' => [$this->date_from, $this->date_to] = [$today->subDay()->toDateString(), $today->toDateString()],
            'this_week' => [$this->date_from, $this->date_to] = [$today->startOfWeek()->toDateString(), Carbon::today()->endOfWeek()->toDateString()],
            'last_week' => [$this->date_from, $this->date_to] = [$today->subWeek()->startOfWeek()->toDateString(), Carbon::today()->subWeek()->endOfWeek()->toDateString()],
            'this_month' => [$this->date_from, $this->date_to] = [Carbon::today()->startOfMonth()->toDateString(), Carbon::today()->endOfMonth()->toDateString()],
            'last_month' => [$this->date_from, $this->date_to] = [Carbon::today()->subMonth()->startOfMonth()->toDateString(), Carbon::today()->subMonth()->endOfMonth()->toDateString()],
            'this_year' => [$this->date_from, $this->date_to] = [Carbon::today()->startOfYear()->toDateString(), Carbon::today()->endOfYear()->toDateString()],
            default => null,
        };
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->where('type', 'out')
                    ->when($this->date_from, fn(Builder $query) => $query->whereDate('transaction_date', '>=', $this->date_from))
                    ->when($this->date_to, fn(Builder $query) => $query->whereDate('transaction_date', '<=', $this->date_to))
                    ->with('product.category')
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.product_code')
                    ->label('Product Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.product_name')
                    ->label('Product')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('product.category.name')
                    ->label('Category')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('PHP')
                    ->label('Selling Price')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PHP')
                    ->label('Revenue')
                    ->weight('bold')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('profit')
                    ->money('PHP')
                    ->label('Profit')
                    ->color('success')
                    ->alignEnd()
                    ->getStateUsing(fn(Transaction $record) => $record->profit),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('product.category', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('No sales found')
            ->emptyStateDescription('No sales transactions for the selected date range.');
    }

    public function getSummaryData(): array
    {
        $query = Transaction::query()
            ->where('type', 'out')
            ->when($this->date_from, fn(Builder $q) => $q->whereDate('transaction_date', '>=', $this->date_from))
            ->when($this->date_to, fn(Builder $q) => $q->whereDate('transaction_date', '<=', $this->date_to));

        $totalSales = $query->sum('total_amount');
        $totalTransactions = $query->count();

        $transactions = $query->with('product')->get();
        $totalProfit = $transactions->sum(function ($t) {
            return ($t->unit_price - ($t->product->cost_price ?? 0)) * $t->quantity;
        });
        $totalQuantity = $transactions->sum('quantity');

        return [
            'total_sales' => $totalSales,
            'total_profit' => $totalProfit,
            'total_transactions' => $totalTransactions,
            'total_quantity' => $totalQuantity,
        ];
    }
}
