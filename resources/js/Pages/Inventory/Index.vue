<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InventoryTable from '@/Components/InventoryTable.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    units: {
        type: Array,
        default: () => [],
    },
});

// Filter state
const searchQuery = ref(props.filters.search || '');
const selectedUnit = ref(props.filters.unit || '');
const selectedStatus = ref(props.filters.status || '');
const sortBy = ref(props.filters.sort || 'name');
const sortDirection = ref(props.filters.direction || 'asc');

// Status options
const statusOptions = [
    { value: '', label: 'All Status' },
    { value: 'in_stock', label: 'In Stock (≥10)' },
    { value: 'low', label: 'Low Stock (<10)' },
    { value: 'out_of_stock', label: 'Out of Stock' },
];

// Sort options
const sortOptions = [
    { value: 'name', label: 'Name' },
    { value: 'quantity', label: 'Quantity' },
    { value: 'created_at', label: 'Date Added' },
];

// Check if any filter is active
const hasActiveFilters = computed(() => {
    return searchQuery.value || selectedUnit.value || selectedStatus.value || sortBy.value !== 'name' || sortDirection.value !== 'asc';
});

// Apply filters
const applyFilters = debounce(() => {
    router.get(
        route('inventory.index'),
        {
            search: searchQuery.value || undefined,
            unit: selectedUnit.value || undefined,
            status: selectedStatus.value || undefined,
            sort: sortBy.value !== 'name' ? sortBy.value : undefined,
            direction: sortDirection.value !== 'asc' ? sortDirection.value : undefined,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}, 300);

// Clear all filters
const clearFilters = () => {
    searchQuery.value = '';
    selectedUnit.value = '';
    selectedStatus.value = '';
    sortBy.value = 'name';
    sortDirection.value = 'asc';
    router.get(route('inventory.index'));
};

// Toggle sort direction
const toggleSortDirection = () => {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    applyFilters();
};

// Watch for filter changes
watch([searchQuery, selectedUnit, selectedStatus, sortBy], () => {
    applyFilters();
});
</script>

<template>
    <Head title="Inventory" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Inventory
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('inventory.create')"
                        class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                    >
                        <svg
                            class="-ml-0.5 mr-1.5 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Add Items
                    </Link>
                    <Link
                        :href="route('inventory.deduct')"
                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                    >
                        <svg
                            class="-ml-0.5 mr-1.5 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20 12H4"
                            />
                        </svg>
                        Deduct Items
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <div
                    v-if="$page.props.flash?.success"
                    class="mb-4 rounded-md bg-green-50 p-4"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-green-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="mb-6 overflow-hidden rounded-lg bg-white p-4 shadow">
                    <!-- Search Bar -->
                    <div class="relative mb-4">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-5 w-5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <TextInput
                            v-model="searchQuery"
                            type="text"
                            class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                            placeholder="Search items by name..."
                        />
                    </div>

                    <!-- Filter Row -->
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Unit Filter -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Unit:</label>
                            <select
                                v-model="selectedUnit"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Units</option>
                                <option v-for="unit in units" :key="unit" :value="unit">
                                    {{ unit.toUpperCase() }}
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Status:</label>
                            <select
                                v-model="selectedStatus"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Sort:</label>
                            <select
                                v-model="sortBy"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option
                                    v-for="option in sortOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <button
                                @click="toggleSortDirection"
                                class="rounded-md p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                                :title="sortDirection === 'asc' ? 'Ascending' : 'Descending'"
                            >
                                <svg
                                    v-if="sortDirection === 'asc'"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"
                                    />
                                </svg>
                            </button>
                        </div>

                        <!-- Clear Filters -->
                        <button
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="ml-auto inline-flex items-center rounded-md bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200"
                        >
                            <svg
                                class="-ml-0.5 mr-1.5 h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                            Clear Filters
                        </button>
                    </div>

                    <!-- Active Filters Summary -->
                    <div
                        v-if="hasActiveFilters"
                        class="mt-3 flex flex-wrap items-center gap-2 border-t border-gray-200 pt-3"
                    >
                        <span class="text-sm text-gray-500">Active filters:</span>
                        <span
                            v-if="searchQuery"
                            class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                        >
                            Search: "{{ searchQuery }}"
                        </span>
                        <span
                            v-if="selectedUnit"
                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800"
                        >
                            Unit: {{ selectedUnit.toUpperCase() }}
                        </span>
                        <span
                            v-if="selectedStatus"
                            class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800"
                        >
                            {{ statusOptions.find(o => o.value === selectedStatus)?.label }}
                        </span>
                        <span
                            v-if="sortBy !== 'name' || sortDirection !== 'asc'"
                            class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800"
                        >
                            Sort: {{ sortOptions.find(o => o.value === sortBy)?.label }} ({{ sortDirection === 'asc' ? '↑' : '↓' }})
                        </span>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <InventoryTable :items="items">
                        <template #actions="{ item }">
                            <div class="flex items-center justify-end gap-3">
                                <Link
                                    :href="route('inventory.edit', item.id)"
                                    class="text-indigo-600 hover:text-indigo-800"
                                    title="Edit item"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>
                                <Link
                                    :href="route('inventory.history', item.id)"
                                    class="text-blue-600 hover:text-blue-800"
                                    title="View history"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </Link>
                            </div>
                        </template>
                    </InventoryTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
