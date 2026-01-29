<?php

namespace App\Services;

use App\Enums\StockStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ItemNotFoundException;
use App\Models\Item;
use App\Models\InventoryTransaction;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Inventory Service
 * 
 * Handles all business logic related to inventory management.
 * Implements the Service Layer pattern to separate business logic
 * from controllers and data access layers.
 * 
 * Design Patterns Used:
 * - Service Layer: Encapsulates business logic
 * - Repository Pattern: Abstracts data access (via ItemRepository)
 * - Transaction Script: Uses database transactions for atomic operations
 * 
 * @package App\Services
 */
class InventoryService
{
    /**
     * Create a new service instance.
     * 
     * @param ItemRepositoryInterface $itemRepository
     */
    public function __construct(
        private ItemRepositoryInterface $itemRepository
    ) {}

    /**
     * Add items to inventory.
     * 
     * If item exists (by name), increases quantity.
     * Otherwise, creates a new item.
     * All operations are wrapped in a database transaction.
     *
     * @param array<int, array{name: string, unit: string, quantity: float, note?: string}> $items
     * @return array<int, Item> Array of updated/created items
     * @throws \Throwable If transaction fails
     */
    public function addItems(array $items): array
    {
        return DB::transaction(function () use ($items) {
            $results = [];

            foreach ($items as $itemData) {
                $item = $this->addSingleItem($itemData);
                $results[] = $item;
                
                Log::info('Inventory added', [
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'quantity_added' => $itemData['quantity'],
                    'new_total' => $item->quantity,
                ]);
            }

            return $results;
        });
    }

    /**
     * Deduct items from inventory.
     * 
     * Validates that deduction doesn't exceed available stock.
     * All operations are wrapped in a database transaction.
     *
     * @param array<int, array{item_id: int, quantity: float, note?: string}> $deductions
     * @return array<int, Item> Array of updated items
     * @throws InsufficientStockException If deduction exceeds available stock
     * @throws ItemNotFoundException If item doesn't exist
     * @throws \Throwable If transaction fails
     */
    public function deductItems(array $deductions): array
    {
        return DB::transaction(function () use ($deductions) {
            $results = [];

            foreach ($deductions as $deduction) {
                $item = $this->deductSingleItem($deduction);
                $results[] = $item;
                
                Log::info('Inventory deducted', [
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'quantity_deducted' => $deduction['quantity'],
                    'remaining' => $item->quantity,
                ]);
            }

            return $results;
        });
    }

    /**
     * Search and filter items.
     *
     * @param array{
     *     search?: string,
     *     unit?: string,
     *     status?: string,
     *     sort?: string,
     *     direction?: string
     * } $filters
     * @return Collection<int, Item>
     */
    public function searchItems(array $filters = []): Collection
    {
        return $this->itemRepository->getAll($filters);
    }

    /**
     * Get inventory history for a specific item.
     *
     * @param int $itemId
     * @return Collection<int, InventoryTransaction>
     * @throws ItemNotFoundException If item doesn't exist
     */
    public function getItemHistory(int $itemId): Collection
    {
        $item = $this->itemRepository->findById($itemId);

        if (!$item) {
            throw new ItemNotFoundException($itemId);
        }

        return $item->transactions()->with('item')->get();
    }

    /**
     * Get dashboard statistics.
     *
     * @return array{
     *     total_items: int,
     *     total_quantity: float,
     *     low_stock_count: int,
     *     out_of_stock_count: int,
     *     recent_transactions: Collection
     * }
     */
    public function getDashboardStats(): array
    {
        return [
            'total_items' => Item::count(),
            'total_quantity' => (float) Item::sum('quantity'),
            'low_stock_count' => Item::lowStock()->count(),
            'out_of_stock_count' => Item::outOfStock()->count(),
            'recent_transactions' => InventoryTransaction::with('item')
                ->recent(5)
                ->get(),
        ];
    }

    /**
     * Get all unique units in use.
     *
     * @return array<int, string>
     */
    public function getUniqueUnits(): array
    {
        return $this->itemRepository->getUniqueUnits();
    }

    /**
     * Get items with low stock levels.
     *
     * @return Collection<int, Item>
     */
    public function getLowStockItems(): Collection
    {
        return $this->itemRepository->getLowStock();
    }

    /**
     * Add a single item to inventory.
     *
     * @param array{name: string, unit: string, quantity: float, note?: string} $data
     * @return Item
     */
    private function addSingleItem(array $data): Item
    {
        $item = $this->itemRepository->findByName($data['name']);

        if ($item) {
            // Item exists - increase quantity
            $item = $this->itemRepository->update($item, [
                'quantity' => $item->quantity + $data['quantity'],
            ]);
        } else {
            // Create new item
            $item = $this->itemRepository->create([
                'name' => $data['name'],
                'unit' => $data['unit'],
                'quantity' => $data['quantity'],
            ]);
        }

        // Log the transaction
        $this->createTransaction(
            $item,
            TransactionType::ADD,
            $data['quantity'],
            $data['note'] ?? null
        );

        return $item->fresh();
    }

    /**
     * Deduct a single item from inventory.
     *
     * @param array{item_id: int, quantity: float, note?: string} $data
     * @return Item
     * @throws InsufficientStockException
     * @throws ItemNotFoundException
     */
    private function deductSingleItem(array $data): Item
    {
        $item = $this->itemRepository->findById($data['item_id']);

        if (!$item) {
            throw new ItemNotFoundException($data['item_id']);
        }

        // Validate stock availability
        if (!$item->canDeduct($data['quantity'])) {
            throw new InsufficientStockException(
                $item->name,
                $item->quantity,
                $data['quantity']
            );
        }

        // Deduct quantity
        $item = $this->itemRepository->update($item, [
            'quantity' => $item->quantity - $data['quantity'],
        ]);

        // Log the transaction
        $this->createTransaction(
            $item,
            TransactionType::DEDUCT,
            $data['quantity'],
            $data['note'] ?? null
        );

        return $item->fresh();
    }

    /**
     * Log quantity adjustment when item is manually edited.
     * 
     * Creates appropriate transaction record based on whether
     * quantity increased or decreased.
     *
     * @param Item $item
     * @param float $oldQuantity
     * @param float $newQuantity
     * @param string $note
     * @return void
     */
    public function logQuantityAdjustment(
        Item $item,
        float $oldQuantity,
        float $newQuantity,
        string $note = 'Manual adjustment'
    ): void {
        $difference = abs($newQuantity - $oldQuantity);

        if ($difference == 0) {
            return;
        }

        $type = $newQuantity > $oldQuantity
            ? TransactionType::ADD
            : TransactionType::DEDUCT;

        $this->createTransaction($item, $type, $difference, $note);

        Log::info('Inventory manually adjusted', [
            'item_id' => $item->id,
            'name' => $item->name,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'difference' => $difference,
            'type' => $type->value,
        ]);
    }

    /**
     * Create an inventory transaction record.
     *
     * @param Item $item
     * @param TransactionType $type
     * @param float $quantity
     * @param string|null $note
     * @return InventoryTransaction
     */
    private function createTransaction(
        Item $item,
        TransactionType $type,
        float $quantity,
        ?string $note
    ): InventoryTransaction {
        return InventoryTransaction::create([
            'item_id' => $item->id,
            'type' => $type,
            'quantity' => $quantity,
            'note' => $note,
        ]);
    }
}
