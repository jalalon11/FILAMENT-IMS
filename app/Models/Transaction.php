<?php

namespace App\Models;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'unit_price',
        'total_amount',
        'reference_number',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if this is a stock in transaction
     */
    public function isStockIn(): bool
    {
        return $this->type === 'in';
    }

    /**
     * Check if this is a stock out transaction
     */
    public function isStockOut(): bool
    {
        return $this->type === 'out';
    }

    /**
     * Calculate profit for this transaction (only for out transactions)
     */
    public function getProfitAttribute(): float
    {
        if ($this->type !== 'out') {
            return 0;
        }

        $costPrice = $this->product->cost_price ?? 0;
        return ($this->unit_price - $costPrice) * $this->quantity;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Update product quantity when transaction is created
        static::created(function (Transaction $transaction) {
            $product = $transaction->product;

            if ($transaction->type === 'in') {
                $product->increment('quantity', $transaction->quantity);

                // Log activity
                ActivityLog::log(
                    'stock_in',
                    "Stock In: {$transaction->quantity} {$product->unit_of_measure} of '{$product->product_name}' received",
                    $transaction,
                    ['product' => $product->product_name, 'quantity' => $transaction->quantity, 'total' => $transaction->total_amount]
                );
            } else {
                $product->decrement('quantity', $transaction->quantity);

                // Log activity
                ActivityLog::log(
                    'stock_out',
                    "Stock Out: {$transaction->quantity} {$product->unit_of_measure} of '{$product->product_name}' sold/released",
                    $transaction,
                    ['product' => $product->product_name, 'quantity' => $transaction->quantity, 'total' => $transaction->total_amount]
                );
            }
        });

        // Reverse quantity change when transaction is deleted
        static::deleted(function (Transaction $transaction) {
            $product = $transaction->product;

            if ($transaction->type === 'in') {
                $product->decrement('quantity', $transaction->quantity);
            } else {
                $product->increment('quantity', $transaction->quantity);
            }
        });
    }
}
