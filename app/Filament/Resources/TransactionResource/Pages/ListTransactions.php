<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk_transaction')
                ->label('New Transaction')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(fn() => route('filament.admin.pages.bulk-transaction'))
                ->color('success'),
        ];
    }
}
