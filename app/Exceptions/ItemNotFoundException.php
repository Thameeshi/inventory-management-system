<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when an item is not found in the inventory.
 */
class ItemNotFoundException extends Exception
{
    public function __construct(
        public readonly int|string $itemIdentifier,
    ) {
        $message = sprintf("Item with identifier '%s' not found.", $itemIdentifier);
        parent::__construct($message, 404);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => 'Item Not Found',
            'message' => $this->getMessage(),
        ], 404);
    }
}
