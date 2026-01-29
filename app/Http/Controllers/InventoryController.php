<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\DeductInventoryRequest;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Inventory Controller
 * 
 * Handles HTTP requests for inventory management operations.
 * Follows the Single Responsibility Principle by delegating
 * business logic to the InventoryService.
 * 
 * @package App\Http\Controllers
 */
class InventoryController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * Uses constructor dependency injection for the service layer.
     * This makes the controller easily testable by allowing
     * mock services to be injected.
     */
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    /**
     * Display a listing of inventory items.
     * 
     * Supports filtering by:
     * - search: Text search in item names
     * - unit: Filter by measurement unit
     * - status: Filter by stock status (in_stock, low, out_of_stock)
     * - sort: Sort field (name, quantity, created_at)
     * - direction: Sort direction (asc, desc)
     */
    public function index(Request $request): Response
    {
        $filters = $this->extractFilters($request);
        $items = $this->inventoryService->searchItems($filters);

        return Inertia::render('Inventory/Index', [
            'items' => $items,
            'filters' => $filters,
            'units' => $this->getAvailableUnits(),
        ]);
    }

    /**
     * Show the form for adding items to inventory.
     */
    public function create(): Response
    {
        return Inertia::render('Inventory/Create', [
            'units' => $this->getAvailableUnits(),
        ]);
    }

    /**
     * Store newly created items in inventory.
     * 
     * Uses Form Request validation for security.
     * All input is validated before processing.
     */
    public function store(StoreInventoryRequest $request): RedirectResponse
    {
        $this->inventoryService->addItems($request->validated()['items']);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Items added successfully!');
    }

    /**
     * Show the form for deducting items from inventory.
     * 
     * Only displays items with available stock (quantity > 0).
     */
    public function showDeduct(): Response
    {
        $items = Item::available()->orderBy('name')->get();

        return Inertia::render('Inventory/Deduct', [
            'items' => $items,
        ]);
    }

    /**
     * Deduct items from inventory.
     * 
     * Validates stock availability before processing.
     * Returns error if deduction exceeds available stock.
     */
    public function deduct(DeductInventoryRequest $request): RedirectResponse
    {
        try {
            $this->inventoryService->deductItems($request->validated()['deductions']);

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Items deducted successfully!');
        } catch (InsufficientStockException $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the transaction history for a specific item.
     * 
     * Uses route model binding for automatic 404 handling.
     */
    public function history(Item $item): Response
    {
        $transactions = $this->inventoryService->getItemHistory($item->id);

        return Inertia::render('Inventory/History', [
            'item' => $item,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the form for editing an item.
     * 
     * Uses route model binding for automatic 404 handling.
     */
    public function edit(Item $item): Response
    {
        return Inertia::render('Inventory/Edit', [
            'item' => $item,
            'units' => $this->getAvailableUnits(),
        ]);
    }

    /**
     * Update the specified item in storage.
     * 
     * Uses Form Request validation for security.
     * Logs the quantity change if quantity was modified.
     */
    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $validated = $request->validated();
        $oldQuantity = $item->quantity;
        $newQuantity = $validated['quantity'];

        // Update item
        $item->update($validated);

        // Log quantity change if modified
        if ($oldQuantity != $newQuantity) {
            $this->inventoryService->logQuantityAdjustment(
                $item,
                $oldQuantity,
                $newQuantity,
                $validated['note'] ?? 'Manual quantity adjustment'
            );
        }

        return redirect()
            ->route('inventory.index')
            ->with('success', "Item '{$item->name}' updated successfully!");
    }

    /**
     * Remove the specified item from storage.
     * 
     * Deletes item and all associated transaction history.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $itemName = $item->name;
        
        // Delete associated transactions first
        $item->transactions()->delete();
        $item->delete();

        return redirect()
            ->route('inventory.index')
            ->with('success', "Item '{$itemName}' deleted successfully!");
    }

    /**
     * Extract and sanitize filter parameters from request.
     * 
     * @param Request $request
     * @return array<string, mixed>
     */
    private function extractFilters(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'unit' => $request->get('unit'),
            'status' => $request->get('status'),
            'sort' => $request->get('sort', 'name'),
            'direction' => $request->get('direction', 'asc'),
        ];
    }

    /**
     * Get list of available measurement units.
     * 
     * @return array<int, string>
     */
    private function getAvailableUnits(): array
    {
        return ['kg', 'm', 'cm', 'pcs', 'ltr', 'box'];
    }
}
