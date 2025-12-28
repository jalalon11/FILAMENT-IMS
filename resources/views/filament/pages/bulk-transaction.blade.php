<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div class="text-lg font-semibold">
                        Grand Total:
                        <span class="text-primary-600 dark:text-primary-400">
                            ₱{{ number_format($this->getGrandTotal(), 2) }}
                        </span>
                    </div>

                    <x-filament::button type="submit" size="lg">
                        <x-slot name="icon">
                            <x-heroicon-o-check class="w-5 h-5" />
                        </x-slot>
                        Save All Transactions
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>
    </form>
</x-filament-panels::page>