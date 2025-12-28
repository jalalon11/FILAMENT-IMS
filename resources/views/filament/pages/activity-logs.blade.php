<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            System Activity Logs
        </x-slot>
        <x-slot name="description">
            Track all user activities including stock movements, product changes, and system events.
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>