<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add database indexes for improved query performance.
 * 
 * Indexes are added to frequently queried columns to optimize:
 * - Search operations on item names
 * - Filtering by unit type
 * - Sorting by quantity
 * - Transaction lookups by type
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Index for search by name (already unique, but adding explicit index)
            $table->index('unit', 'idx_items_unit');
            $table->index('quantity', 'idx_items_quantity');
            $table->index(['quantity', 'unit'], 'idx_items_quantity_unit');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Index for filtering transactions by type
            $table->index('type', 'idx_transactions_type');
            // Composite index for common queries
            $table->index(['item_id', 'type'], 'idx_transactions_item_type');
            $table->index(['item_id', 'created_at'], 'idx_transactions_item_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('idx_items_unit');
            $table->dropIndex('idx_items_quantity');
            $table->dropIndex('idx_items_quantity_unit');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_type');
            $table->dropIndex('idx_transactions_item_type');
            $table->dropIndex('idx_transactions_item_date');
        });
    }
};
