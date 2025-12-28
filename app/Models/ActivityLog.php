<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Log an activity
     */
    public static function log(string $action, string $description, ?Model $model = null, ?array $properties = null): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Get icon for action type
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created' => 'heroicon-o-plus-circle',
            'updated' => 'heroicon-o-pencil-square',
            'deleted' => 'heroicon-o-trash',
            'stock_in' => 'heroicon-o-arrow-down-tray',
            'stock_out' => 'heroicon-o-arrow-up-tray',
            'login' => 'heroicon-o-arrow-right-on-rectangle',
            'logout' => 'heroicon-o-arrow-left-on-rectangle',
            default => 'heroicon-o-information-circle',
        };
    }

    /**
     * Get color for action type
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created', 'stock_in' => 'success',
            'updated' => 'info',
            'deleted', 'stock_out' => 'danger',
            'login', 'logout' => 'warning',
            default => 'gray',
        };
    }
}
