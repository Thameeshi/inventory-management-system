<?php

namespace App\Models;

use App\Enums\StockStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Item Model
 * 
 * Represents an inventory item with its current stock level.
 * Uses query scopes for common filtering operations and
 * accessors for computed properties.
 *
 * @property int $id
 * @property string $name
 * @property string $unit
 * @property float $quantity
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<InventoryTransaction> $transactions
 * @property-read StockStatus $status
 */
class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'unit',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['status', 'status_color'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the inventory transactions for the item.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class)->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the stock status of the item.
     */
    public function getStatusAttribute(): string
    {
        return StockStatus::fromQuantity($this->quantity)->value;
    }

    /**
     * Get the status color for UI display.
     */
    public function getStatusColorAttribute(): string
    {
        return StockStatus::fromQuantity($this->quantity)->color();
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return StockStatus::fromQuantity($this->quantity)->label();
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Filter items with low stock.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0)
                     ->where('quantity', '<', StockStatus::LOW_STOCK_THRESHOLD);
    }

    /**
     * Scope: Filter items that are in stock (quantity >= threshold).
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('quantity', '>=', StockStatus::LOW_STOCK_THRESHOLD);
    }

    /**
     * Scope: Filter items that are out of stock.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope: Filter items that have available stock (quantity > 0).
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope: Search items by name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope: Filter by unit type.
     */
    public function scopeOfUnit(Builder $query, string $unit): Builder
    {
        return $query->where('unit', $unit);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the item has low stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity < StockStatus::LOW_STOCK_THRESHOLD;
    }

    /**
     * Check if the item is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    /**
     * Check if requested quantity can be deducted.
     */
    public function canDeduct(float $quantity): bool
    {
        return $this->quantity >= $quantity;
    }

    /**
     * Get the total additions for this item.
     */
    public function getTotalAdditions(): float
    {
        return $this->transactions()->where('type', 'add')->sum('quantity');
    }

    /**
     * Get the total deductions for this item.
     */
    public function getTotalDeductions(): float
    {
        return $this->transactions()->where('type', 'deduct')->sum('quantity');
    }
}
