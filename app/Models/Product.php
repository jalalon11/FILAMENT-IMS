<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'product_name',
        'quantity',
        'unit_of_measure',
        'category_id',
        'company_id',
        'cost_price',
        'selling_price',
        'date_received',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'date_received' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the profit margin per unit
     */
    public function getProfitMarginAttribute(): float
    {
        return $this->selling_price - $this->cost_price;
    }

    /**
     * Get the total stock value at cost
     */
    public function getStockValueAttribute(): float
    {
        return $this->quantity * $this->cost_price;
    }

    /**
     * Get the total potential revenue at selling price
     */
    public function getPotentialRevenueAttribute(): float
    {
        return $this->quantity * $this->selling_price;
    }
}
