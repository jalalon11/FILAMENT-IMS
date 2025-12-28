<div class="fixed bottom-6 right-6 z-50" x-data="{ open: @entangle('isOpen') }">
    {{-- Quick Action Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" @click.away="$wire.close()"
        class="absolute bottom-16 right-0 mb-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="p-2 border-b border-gray-100 dark:border-gray-700">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-2">Quick
                Actions</span>
        </div>

        <div class="p-1">
            {{-- New Transaction --}}
            <a href="{{ route('filament.admin.resources.transactions.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <x-heroicon-o-arrows-right-left class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">New Transaction</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Stock In/Out</p>
                </div>
            </a>

            {{-- Add Product --}}
            <a href="{{ route('filament.admin.resources.products.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-heroicon-o-cube class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Add Product</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">New inventory item</p>
                </div>
            </a>

            {{-- Add Category --}}
            <a href="{{ route('filament.admin.resources.categories.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <x-heroicon-o-tag class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Add Category</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Organize products</p>
                </div>
            </a>

            {{-- Add Company --}}
            <a href="{{ route('filament.admin.resources.companies.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <x-heroicon-o-building-office class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Add Company</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Supplier/Vendor</p>
                </div>
            </a>
        </div>

        <div class="p-1 border-t border-gray-100 dark:border-gray-700">
            {{-- Sales Report --}}
            <a href="{{ route('filament.admin.pages.sales-report') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                <div class="w-9 h-9 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <x-heroicon-o-document-chart-bar class="w-5 h-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Sales Report</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">View analytics</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Floating Action Button --}}
    <button wire:click="toggle"
        class="w-14 h-14 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center group"
        :class="{ 'rotate-45': open }">
        <x-heroicon-o-plus class="w-7 h-7 transition-transform duration-200" ::class="{ 'rotate-45': open }" />
    </button>
</div>