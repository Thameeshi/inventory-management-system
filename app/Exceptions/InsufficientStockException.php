<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when attempting to deduct more stock than available.
 * 
 * This exception provides detailed information about the failed deduction
 * attempt, including available stock and requested quantity.
 */
class InsufficientStockException extends Exception
{
    public function __construct(
        public readonly string $itemName,
        public readonly float $availableStock,
        public readonly float $requestedQuantity,
    ) {
        $message = sprintf(
            "Insufficient stock for item '%s'. Available: %.2f, Requested: %.2f",
            $itemName,
            $availableStock,
            $requestedQuantity
        );

        parent::__construct($message, 422);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => 'Insufficient Stock',
            'message' => $this->getMessage(),
            'item' => $this->itemName,
            'available' => $this->availableStock,
            'requested' => $this->requestedQuantity,
        ], 422);
    }
}
