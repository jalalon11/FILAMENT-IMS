<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Total products count
        $totalProducts = Product::count();

        // Total stock value (at cost)
        $totalStockValue = Product::sum(\DB::raw('quantity * cost_price'));

        // Today's sales (out transactions)
        $todaySales = Transaction::where('type', 'out')
            ->whereDate('transaction_date', today())
            ->sum('total_amount');

        // Today's profit
        $todayTransactions = Transaction::where('type', 'out')
            ->whereDate('transaction_date', today())
            ->with('product')
            ->get();

        $todayProfit = $todayTransactions->sum(function ($transaction) {
            return ($transaction->unit_price - $transaction->product->cost_price) * $transaction->quantity;
        });

        // Monthly sales
        $monthlySales = Transaction::where('type', 'out')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('total_amount');

        // Monthly profit
        $monthlyTransactions = Transaction::where('type', 'out')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->with('product')
            ->get();

        $monthlyProfit = $monthlyTransactions->sum(function ($transaction) {
            return ($transaction->unit_price - $transaction->product->cost_price) * $transaction->quantity;
        });

        // Low stock count
        $lowStockCount = Product::where('quantity', '<=', 10)->count();

        return [
            Stat::make('Total Products', number_format($totalProducts))
                ->description('Active products in inventory')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Stock Value', '₱' . number_format($totalStockValue, 2))
                ->description('Total inventory value at cost')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            Stat::make('Today\'s Sales', '₱' . number_format($todaySales, 2))
                ->description('Profit: ₱' . number_format($todayProfit, 2))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),

            Stat::make('Monthly Sales', '₱' . number_format($monthlySales, 2))
                ->description('Net Profit: ₱' . number_format($monthlyProfit, 2))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make('Low Stock Items', $lowStockCount)
                ->description('Products with ≤10 units')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'warning' : 'success'),
        ];
    }
}
