<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Complete Transaction History
        </x-slot>
        <x-slot name="description">
            View all stock in and stock out transactions with detailed filters.
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>