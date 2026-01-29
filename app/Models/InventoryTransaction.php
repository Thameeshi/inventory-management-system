<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * InventoryTransaction Model
 * 
 * Represents a single inventory transaction (addition or deduction).
 * Maintains an audit trail of all inventory movements for traceability.
 *
 * @property int $id
 * @property int $item_id
 * @property string $type
 * @property float $quantity
 * @property string|null $note
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Item $item
 */
class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'type' => TransactionType::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['type_label', 'type_color'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the item that owns the transaction.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get human-readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    /**
     * Get type color for UI.
     */
    public function getTypeColorAttribute(): string
    {
        return $this->type->color();
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Filter additions only.
     */
    public function scopeAdditions(Builder $query): Builder
    {
        return $query->where('type', TransactionType::ADD);
    }

    /**
     * Scope: Filter deductions only.
     */
    public function scopeDeductions(Builder $query): Builder
    {
        return $query->where('type', TransactionType::DEDUCT);
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeBetweenDates(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope: Recent transactions.
     */
    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->latest()->limit($limit);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the transaction is an addition.
     */
    public function isAddition(): bool
    {
        return $this->type === TransactionType::ADD;
    }

    /**
     * Check if the transaction is a deduction.
     */
    public function isDeduction(): bool
    {
        return $this->type === TransactionType::DEDUCT;
    }

    /**
     * Get signed quantity (positive for add, negative for deduct).
     */
    public function getSignedQuantity(): float
    {
        return $this->isAddition() ? $this->quantity : -$this->quantity;
    }
}
