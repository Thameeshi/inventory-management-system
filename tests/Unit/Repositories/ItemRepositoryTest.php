<?php

namespace Tests\Unit\Repositories;

use App\Enums\StockStatus;
use App\Models\Item;
use App\Repositories\ItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ItemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ItemRepository();
    }

    public function test_can_find_item_by_id(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 10,
        ]);

        $found = $this->repository->findById($item->id);

        $this->assertNotNull($found);
        $this->assertEquals($item->id, $found->id);
        $this->assertEquals('Test Item', $found->name);
    }

    public function test_returns_null_when_item_not_found(): void
    {
        $found = $this->repository->findById(999);

        $this->assertNull($found);
    }

    public function test_can_find_item_by_name(): void
    {
        Item::create([
            'name' => 'Unique Item',
            'unit' => 'kg',
            'quantity' => 5,
        ]);

        $found = $this->repository->findByName('Unique Item');

        $this->assertNotNull($found);
        $this->assertEquals('Unique Item', $found->name);
    }

    public function test_can_get_all_items(): void
    {
        Item::create(['name' => 'Item 1', 'unit' => 'pcs', 'quantity' => 10]);
        Item::create(['name' => 'Item 2', 'unit' => 'kg', 'quantity' => 5]);
        Item::create(['name' => 'Item 3', 'unit' => 'ltr', 'quantity' => 2]);

        $items = $this->repository->getAll();

        $this->assertCount(3, $items);
    }

    public function test_can_filter_items_by_stock_status(): void
    {
        Item::create(['name' => 'In Stock', 'unit' => 'pcs', 'quantity' => 10]);
        Item::create(['name' => 'Low Stock', 'unit' => 'kg', 'quantity' => 3]);
        Item::create(['name' => 'Out of Stock', 'unit' => 'ltr', 'quantity' => 0]);

        $lowStock = $this->repository->getAll(['status' => StockStatus::LOW_STOCK->value]);

        $this->assertCount(1, $lowStock);
        $this->assertEquals('Low Stock', $lowStock->first()->name);
    }

    public function test_can_search_items_by_name(): void
    {
        Item::create(['name' => 'Apple Juice', 'unit' => 'ltr', 'quantity' => 10]);
        Item::create(['name' => 'Orange Juice', 'unit' => 'ltr', 'quantity' => 5]);
        Item::create(['name' => 'Water Bottle', 'unit' => 'pcs', 'quantity' => 20]);

        $results = $this->repository->getAll(['search' => 'Juice']);

        $this->assertCount(2, $results);
    }

    public function test_can_sort_items(): void
    {
        Item::create(['name' => 'Zebra', 'unit' => 'pcs', 'quantity' => 10]);
        Item::create(['name' => 'Apple', 'unit' => 'kg', 'quantity' => 5]);
        Item::create(['name' => 'Mango', 'unit' => 'pcs', 'quantity' => 8]);

        $items = $this->repository->getAll(['sort' => 'name', 'direction' => 'asc']);

        $this->assertEquals('Apple', $items->first()->name);
        $this->assertEquals('Zebra', $items->last()->name);
    }

    public function test_can_create_item(): void
    {
        $data = [
            'name' => 'New Item',
            'unit' => 'pcs',
            'quantity' => 15,
        ];

        $item = $this->repository->create($data);

        $this->assertNotNull($item);
        $this->assertEquals('New Item', $item->name);
        $this->assertDatabaseHas('items', $data);
    }

    public function test_can_update_item(): void
    {
        $item = Item::create([
            'name' => 'Old Name',
            'unit' => 'pcs',
            'quantity' => 10,
        ]);

        $updated = $this->repository->update($item, [
            'name' => 'New Name',
            'quantity' => 20,
        ]);

        $this->assertInstanceOf(Item::class, $updated);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'New Name',
            'quantity' => 20,
        ]);
    }

    public function test_can_delete_item(): void
    {
        $item = Item::create([
            'name' => 'To Delete',
            'unit' => 'pcs',
            'quantity' => 10,
        ]);

        $deleted = $this->repository->delete($item);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);
    }

    public function test_can_get_low_stock_items(): void
    {
        Item::create(['name' => 'In Stock', 'unit' => 'pcs', 'quantity' => 100]);
        Item::create(['name' => 'Low Stock 1', 'unit' => 'kg', 'quantity' => 4]);
        Item::create(['name' => 'Low Stock 2', 'unit' => 'ltr', 'quantity' => 2]);

        $lowStock = $this->repository->getLowStock();

        $this->assertCount(2, $lowStock);
    }

    public function test_can_get_out_of_stock_items(): void
    {
        Item::create(['name' => 'In Stock', 'unit' => 'pcs', 'quantity' => 100]);
        Item::create(['name' => 'Out of Stock 1', 'unit' => 'kg', 'quantity' => 0]);
        Item::create(['name' => 'Out of Stock 2', 'unit' => 'ltr', 'quantity' => 0]);

        $outOfStock = $this->repository->getOutOfStock();

        $this->assertCount(2, $outOfStock);
    }

    public function test_can_get_unique_units(): void
    {
        Item::create(['name' => 'Item 1', 'unit' => 'pcs', 'quantity' => 10]);
        Item::create(['name' => 'Item 2', 'unit' => 'kg', 'quantity' => 5]);
        Item::create(['name' => 'Item 3', 'unit' => 'pcs', 'quantity' => 8]);

        $units = $this->repository->getUniqueUnits();

        $this->assertCount(2, $units);
        $this->assertContains('pcs', $units);
        $this->assertContains('kg', $units);
    }
}
