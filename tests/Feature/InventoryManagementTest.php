<?php

namespace Tests\Feature;

use App\Enums\StockStatus;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_inventory_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('inventory.index'));

        $response->assertStatus(200);
    }

    public function test_can_add_items_to_inventory(): void
    {
        $data = [
            'items' => [
                [
                    'name' => 'New Item',
                    'unit' => 'pcs',
                    'quantity' => 10,
                    'note' => 'Initial stock'
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'name' => 'New Item',
            'quantity' => 10,
        ]);
    }

    public function test_can_deduct_items_from_inventory(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 20,
        ]);

        $data = [
            'deductions' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 5,
                    'note' => 'Testing deduction'
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.deduct.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'quantity' => 15,
        ]);
    }

    public function test_cannot_deduct_more_than_available_quantity(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 5,
        ]);

        $data = [
            'deductions' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 10,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.deduct.store'), $data);

        $response->assertSessionHasErrors();
    }

    public function test_can_view_transaction_history(): void
    {
        $item = Item::create([
            'name' => 'Test Item',
            'unit' => 'pcs',
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('inventory.history', $item));

        $response->assertStatus(200);
    }

    public function test_can_view_low_stock_items(): void
    {
        Item::create(['name' => 'Normal Stock', 'unit' => 'pcs', 'quantity' => 100]);
        Item::create(['name' => 'Low Stock', 'unit' => 'kg', 'quantity' => 3]);

        $response = $this->actingAs($this->user)
            ->get(route('inventory.index', ['status' => StockStatus::LOW_STOCK->value]));

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_inventory(): void
    {
        $response = $this->get(route('inventory.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_validation_requires_item_name(): void
    {
        $data = [
            'items' => [
                [
                    'unit' => 'pcs',
                    'quantity' => 10,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $data);

        $response->assertSessionHasErrors(['items.0.name']);
    }

    public function test_validation_requires_positive_quantity(): void
    {
        $data = [
            'items' => [
                [
                    'name' => 'Test Item',
                    'unit' => 'pcs',
                    'quantity' => -5,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $data);

        $response->assertSessionHasErrors(['items.0.quantity']);
    }
}
