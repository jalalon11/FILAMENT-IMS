<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class CategoryBreakdownChart extends ChartWidget
{
    protected static ?string $heading = 'Sales by Category';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $categories = Category::withCount([
            'products as sold_count' => function ($query) {
                $query->whereHas('transactions', function ($q) {
                    $q->where('type', 'out')
                        ->whereMonth('transaction_date', now()->month)
                        ->whereYear('transaction_date', now()->year);
                });
            }
        ])->get();

        // Get actual sales amounts per category
        $categoryData = [];
        $categoryLabels = [];
        $backgroundColors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(34, 197, 94, 0.7)',
            'rgba(249, 115, 22, 0.7)',
            'rgba(139, 92, 246, 0.7)',
            'rgba(236, 72, 153, 0.7)',
            'rgba(20, 184, 166, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(99, 102, 241, 0.7)',
        ];

        foreach ($categories as $index => $category) {
            $totalSales = Transaction::where('type', 'out')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->whereHas('product', function ($query) use ($category) {
                    $query->where('category_id', $category->id);
                })
                ->sum('total_amount');

            if ($totalSales > 0) {
                $categoryLabels[] = $category->name;
                $categoryData[] = $totalSales;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales Amount',
                    'data' => $categoryData,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($categoryData)),
                ],
            ],
            'labels' => $categoryLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
