<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\InventoryTransaction;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample items with different units
        $items = [
            ['name' => 'Steel Rod', 'unit' => 'm', 'quantity' => 150.00],
            ['name' => 'Copper Wire', 'unit' => 'm', 'quantity' => 200.50],
            ['name' => 'Cement', 'unit' => 'kg', 'quantity' => 500.00],
            ['name' => 'Sand', 'unit' => 'kg', 'quantity' => 1000.00],
            ['name' => 'Bricks', 'unit' => 'pcs', 'quantity' => 5000.00],
            ['name' => 'Paint - White', 'unit' => 'ltr', 'quantity' => 50.00],
            ['name' => 'Paint - Blue', 'unit' => 'ltr', 'quantity' => 8.50], // Low stock
            ['name' => 'Nails (2 inch)', 'unit' => 'box', 'quantity' => 25.00],
            ['name' => 'Screws (1 inch)', 'unit' => 'box', 'quantity' => 30.00],
            ['name' => 'PVC Pipe', 'unit' => 'm', 'quantity' => 5.00], // Low stock
            ['name' => 'Wooden Planks', 'unit' => 'pcs', 'quantity' => 75.00],
            ['name' => 'Glass Sheets', 'unit' => 'pcs', 'quantity' => 3.00], // Low stock
        ];

        foreach ($items as $itemData) {
            $item = Item::create($itemData);

            // Create initial "add" transaction for each item
            InventoryTransaction::create([
                'item_id' => $item->id,
                'type' => 'add',
                'quantity' => $itemData['quantity'],
                'note' => 'Initial stock',
            ]);
        }

        // Add some additional transactions for history
        $steelRod = Item::where('name', 'Steel Rod')->first();
        if ($steelRod) {
            // Add more stock
            InventoryTransaction::create([
                'item_id' => $steelRod->id,
                'type' => 'add',
                'quantity' => 50.00,
                'note' => 'Received from Supplier ABC',
            ]);
            $steelRod->increment('quantity', 50.00);

            // Deduct stock
            InventoryTransaction::create([
                'item_id' => $steelRod->id,
                'type' => 'deduct',
                'quantity' => 25.00,
                'note' => 'Used for Project X',
            ]);
            $steelRod->decrement('quantity', 25.00);
        }

        $cement = Item::where('name', 'Cement')->first();
        if ($cement) {
            // Deduct stock
            InventoryTransaction::create([
                'item_id' => $cement->id,
                'type' => 'deduct',
                'quantity' => 100.00,
                'note' => 'Construction site A',
            ]);
            $cement->decrement('quantity', 100.00);

            // Add more stock
            InventoryTransaction::create([
                'item_id' => $cement->id,
                'type' => 'add',
                'quantity' => 200.00,
                'note' => 'Monthly supply delivery',
            ]);
            $cement->increment('quantity', 200.00);
        }

        $bricks = Item::where('name', 'Bricks')->first();
        if ($bricks) {
            // Deduct stock
            InventoryTransaction::create([
                'item_id' => $bricks->id,
                'type' => 'deduct',
                'quantity' => 500.00,
                'note' => 'Wall construction',
            ]);
            $bricks->decrement('quantity', 500.00);
        }
    }
}
