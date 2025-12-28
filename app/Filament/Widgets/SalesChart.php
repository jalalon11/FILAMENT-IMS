<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales & Profit';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $months = collect();
        $salesData = collect();
        $profitData = collect();

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M'));

            // Calculate sales for this month
            $monthlySales = Transaction::where('type', 'out')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('total_amount');

            $salesData->push($monthlySales);

            // Calculate profit for this month
            $monthlyTransactions = Transaction::where('type', 'out')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->with('product')
                ->get();

            $monthlyProfit = $monthlyTransactions->sum(function ($transaction) {
                return ($transaction->unit_price - ($transaction->product->cost_price ?? 0)) * $transaction->quantity;
            });

            $profitData->push($monthlyProfit);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $salesData->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Net Profit',
                    'data' => $profitData->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
