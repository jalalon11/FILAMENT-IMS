<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $transaction = $this->record;
        $type = $transaction->type === 'in' ? 'stocked in' : 'sold/released';

        Notification::make()
            ->title('Transaction Recorded')
            ->body("{$transaction->quantity} units of {$transaction->product->product_name} {$type}")
            ->success()
            ->send();
    }
}
