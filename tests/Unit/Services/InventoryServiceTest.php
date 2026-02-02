<?php

namespace Tests\Unit\Services;

use App\Enums\StockStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ItemNotFoundException;
use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InventoryService::class);
    }

    public function test_can_add_new_items(): void
    {
        $items = [
            [
                'name' => 'Test Item',
                'unit' => 'pcs',
                'quantity' => 10,
                'note' => 'Initial stock'
            ]
        ];

        $result = $this->service->addItems($items);

        $this->assertCount(1, $result);
        $this->assertEquals('Test Item', $result[0]->name);
        $this->assertEquals(10, $result[0]->quantity);
        $this->assertDatabaseHas('items', [
            'name' => 'Test Item',
            'quantity' => 10,
        ]);
    }

    public function test_can_increase_quantity_for_existing_item(): void
    {
        Item::create([
            'name' => 'Existing Item',
            'unit' => 'kg',
            'quantity' => 5,
        ]);

        $items = [
            [
                'name' => 'Existing Item',
                'unit' => 'kg',
                'quantity' => 3,
            ]
        ];

        $result = $this->service->addItems($items);

        $this->assertEquals(8, $result[0]->quantity);
        $this->assertDatabaseHas('items', [
            'name' => 'Existing Item',
            'quantity' => 8,
        ]);
    }

    public function test_can_deduct_items(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 10,
        ]);

        $items = [
            [
                'item_id' => $item->id,
                'quantity' => 5,
                'note' => 'Deduction test'
            ]
        ];

        $result = $this->service->deductItems($items);

        $this->assertEquals(5, $result[0]->quantity);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'quantity' => 5,
        ]);
    }

    public function test_throws_exception_when_deducting_more_than_available(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 5,
        ]);

        $items = [
            [
                'item_id' => $item->id,
                'quantity' => 10,
            ]
        ];

        $this->expectException(InsufficientStockException::class);
        $this->service->deductItems($items);
    }

    public function test_throws_exception_when_item_not_found(): void
    {
        $items = [
            [
                'item_id' => 999,
                'quantity' => 5,
            ]
        ];

        $this->expectException(ItemNotFoundException::class);
        $this->service->deductItems($items);
    }

    public function test_updates_stock_status_correctly(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 10,
        ]);

        // Deduct to low stock
        $this->service->deductItems([[
            'item_id' => $item->id,
            'quantity' => 7,
        ]]);

        $item->refresh();
        $this->assertEquals(StockStatus::LOW_STOCK->value, $item->status);

        // Deduct to out of stock
        $this->service->deductItems([[
            'item_id' => $item->id,
            'quantity' => 3,
        ]]);

        $item->refresh();
        $this->assertEquals(StockStatus::OUT_OF_STOCK->value, $item->status);
    }

    public function test_creates_transaction_records(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 10,
        ]);

        $this->service->deductItems([[
            'item_id' => $item->id,
            'quantity' => 5,
            'note' => 'Test deduction'
        ]]);

        $this->assertDatabaseHas('inventory_transactions', [
            'item_id' => $item->id,
            'type' => TransactionType::DEDUCT->value,
            'quantity' => 5,
            'note' => 'Test deduction',
        ]);
    }
}
