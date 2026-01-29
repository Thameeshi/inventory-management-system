<?php

namespace App\Enums;

/**
 * Enum representing inventory transaction types.
 * Provides type safety and centralized management of transaction types.
 */
enum TransactionType: string
{
    case ADD = 'add';
    case DEDUCT = 'deduct';

    /**
     * Get human-readable label for the transaction type.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADD => 'Addition',
            self::DEDUCT => 'Deduction',
        };
    }

    /**
     * Get CSS color class for the transaction type.
     */
    public function color(): string
    {
        return match ($this) {
            self::ADD => 'green',
            self::DEDUCT => 'red',
        };
    }

    /**
     * Check if transaction increases stock.
     */
    public function increasesStock(): bool
    {
        return $this === self::ADD;
    }
}
