<?php

namespace App\Repositories\Contracts;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Item Repository.
 * 
 * Defines the contract for data access operations on Item entities.
 * Implementing the Repository Pattern separates data access logic
 * from business logic, making the code more testable and maintainable.
 */
interface ItemRepositoryInterface
{
    /**
     * Find an item by its ID.
     */
    public function findById(int $id): ?Item;

    /**
     * Find an item by its name.
     */
    public function findByName(string $name): ?Item;

    /**
     * Get all items with optional filters.
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Create a new item.
     */
    public function create(array $data): Item;

    /**
     * Update an existing item.
     */
    public function update(Item $item, array $data): Item;

    /**
     * Delete an item.
     */
    public function delete(Item $item): bool;

    /**
     * Get items with low stock.
     */
    public function getLowStock(): Collection;

    /**
     * Get items that are out of stock.
     */
    public function getOutOfStock(): Collection;

    /**
     * Get all unique units used in inventory.
     */
    public function getUniqueUnits(): array;
}
