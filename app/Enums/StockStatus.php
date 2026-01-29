<?php

namespace App\Enums;

/**
 * Enum representing stock status levels.
 * Used for filtering and displaying inventory status.
 */
enum StockStatus: string
{
    case IN_STOCK = 'in_stock';
    case LOW_STOCK = 'low';
    case OUT_OF_STOCK = 'out_of_stock';

    /**
     * Low stock threshold value.
     */
    public const LOW_STOCK_THRESHOLD = 10;

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::IN_STOCK => 'In Stock',
            self::LOW_STOCK => 'Low Stock',
            self::OUT_OF_STOCK => 'Out of Stock',
        };
    }

    /**
     * Get CSS color class.
     */
    public function color(): string
    {
        return match ($this) {
            self::IN_STOCK => 'green',
            self::LOW_STOCK => 'yellow',
            self::OUT_OF_STOCK => 'red',
        };
    }

    /**
     * Determine status from quantity.
     */
    public static function fromQuantity(float $quantity): self
    {
        if ($quantity <= 0) {
            return self::OUT_OF_STOCK;
        }

        if ($quantity < self::LOW_STOCK_THRESHOLD) {
            return self::LOW_STOCK;
        }

        return self::IN_STOCK;
    }
}
