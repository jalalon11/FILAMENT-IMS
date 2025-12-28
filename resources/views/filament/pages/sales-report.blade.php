<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter Options
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quick Select</label>
                <select wire:model.live="preset"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="last_week">Last Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            @if($preset === 'custom')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                    <input type="date" wire:model.live="date_from"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                    <input type="date" wire:model.live="date_to"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
            @endif
        </div>
    </x-filament::section>

    @php
        $summary = $this->getSummaryData();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-6">
        <x-filament::section>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    ₱{{ number_format($summary['total_sales'], 2) }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Net Profit</p>
                <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                    ₱{{ number_format($summary['total_profit'], 2) }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Transactions</p>
                <p class="text-2xl font-bold text-info-600 dark:text-info-400">
                    {{ number_format($summary['total_transactions']) }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">Items Sold</p>
                <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                    {{ number_format($summary['total_quantity']) }}</p>
            </div>
        </x-filament::section>
    </div>

    <x-filament::section>
        <x-slot name="heading">
            Sales Transactions
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>