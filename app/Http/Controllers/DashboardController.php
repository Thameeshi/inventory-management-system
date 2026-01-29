<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    /**
     * Display the dashboard with inventory statistics.
     */
    public function index(): Response
    {
        $stats = $this->inventoryService->getDashboardStats();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
