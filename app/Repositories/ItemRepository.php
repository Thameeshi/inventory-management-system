<?php

namespace App\Repositories;

use App\Enums\StockStatus;
use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent implementation of Item Repository.
 * 
 * This class handles all database operations for Item entities,
 * encapsulating query logic and providing a clean interface for
 * the service layer to interact with.
 */
class ItemRepository implements ItemRepositoryInterface
{
    /**
     * Allowed fields for sorting.
     */
    private const ALLOWED_SORT_FIELDS = ['name', 'quantity', 'created_at', 'updated_at'];

    /**
     * Default sort field.
     */
    private const DEFAULT_SORT_FIELD = 'name';

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Item
    {
        return Item::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $name): ?Item
    {
        return Item::where('name', $name)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Item::query();

        // Apply search filter (sanitized)
        if (!empty($filters['search'])) {
            $search = $this->sanitizeSearchTerm($filters['search']);
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply unit filter
        if (!empty($filters['unit'])) {
            $query->where('unit', $filters['unit']);
        }

        // Apply stock status filter
        $this->applyStatusFilter($query, $filters['status'] ?? null);

        // Apply sorting
        $this->applySorting(
            $query,
            $filters['sort'] ?? self::DEFAULT_SORT_FIELD,
            $filters['direction'] ?? 'asc'
        );

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Item
    {
        return Item::create([
            'name' => trim($data['name']),
            'unit' => $data['unit'],
            'quantity' => $data['quantity'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item, array $data): Item
    {
        $item->update($data);
        return $item->fresh();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Item $item): bool
    {
        return $item->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getLowStock(): Collection
    {
        return Item::lowStock()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getOutOfStock(): Collection
    {
        return Item::outOfStock()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueUnits(): array
    {
        return Item::distinct()->pluck('unit')->toArray();
    }

    /**
     * Sanitize search term to prevent SQL injection patterns.
     */
    private function sanitizeSearchTerm(string $term): string
    {
        // Remove special characters that could be used in SQL injection
        return preg_replace('/[%_]/', '', trim($term));
    }

    /**
     * Apply stock status filter to query.
     */
    private function applyStatusFilter($query, ?string $status): void
    {
        if (!$status) {
            return;
        }

        $statusEnum = StockStatus::tryFrom($status);
        
        if (!$statusEnum) {
            return;
        }

        match ($statusEnum) {
            StockStatus::LOW_STOCK => $query->lowStock(),
            StockStatus::IN_STOCK => $query->inStock(),
            StockStatus::OUT_OF_STOCK => $query->outOfStock(),
        };
    }

    /**
     * Apply sorting to query with validation.
     */
    private function applySorting($query, string $field, string $direction): void
    {
        $field = in_array($field, self::ALLOWED_SORT_FIELDS) 
            ? $field 
            : self::DEFAULT_SORT_FIELD;

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query->orderBy($field, $direction);
    }
}
