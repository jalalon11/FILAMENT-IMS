<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DailyTrendsChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Transaction Trends (Last 14 Days)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect();
        $stockInData = collect();
        $stockOutData = collect();

        // Get last 14 days
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('M d'));

            // Stock In count for this day
            $stockIn = Transaction::where('type', 'in')
                ->whereDate('transaction_date', $date->toDateString())
                ->sum('quantity');

            $stockInData->push($stockIn);

            // Stock Out count for this day
            $stockOut = Transaction::where('type', 'out')
                ->whereDate('transaction_date', $date->toDateString())
                ->sum('quantity');

            $stockOutData->push($stockOut);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Stock In (Received)',
                    'data' => $stockInData->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'fill' => true,
                ],
                [
                    'label' => 'Stock Out (Sold)',
                    'data' => $stockOutData->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
