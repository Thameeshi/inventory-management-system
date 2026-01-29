<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <StatCard
                        title="Total Items"
                        :value="stats.total_items"
                        icon="package"
                        color="blue"
                    />
                    <StatCard
                        title="Total Quantity"
                        :value="parseFloat(stats.total_quantity).toFixed(2)"
                        icon="chart"
                        color="green"
                    />
                    <StatCard
                        title="Low Stock Items"
                        :value="stats.low_stock_items"
                        icon="warning"
                        :color="stats.low_stock_items > 0 ? 'red' : 'green'"
                    />
                </div>

                <!-- Quick Actions -->
                <div class="mb-8 overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            Quick Actions
                        </h3>
                        <div class="flex flex-wrap gap-4">
                            <Link
                                :href="route('inventory.create')"
                                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
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
                                class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
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
                            <Link
                                :href="route('inventory.index')"
                                class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600"
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
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16"
                                    />
                                </svg>
                                View Inventory
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">
                            Recent Transactions
                        </h3>
                        <div class="overflow-x-auto">
                            <table
                                v-if="stats.recent_transactions.length > 0"
                                class="min-w-full divide-y divide-gray-200"
                            >
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Item
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Type
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Quantity
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr
                                        v-for="transaction in stats.recent_transactions"
                                        :key="transaction.id"
                                    >
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Link
                                                :href="route('inventory.history', transaction.item.id)"
                                                class="text-sm font-medium text-blue-600 hover:text-blue-800"
                                            >
                                                {{ transaction.item.name }}
                                            </Link>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                :class="[
                                                    'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                                    transaction.type === 'add'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800',
                                                ]"
                                            >
                                                {{
                                                    transaction.type === 'add'
                                                        ? 'Addition'
                                                        : 'Deduction'
                                                }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                :class="[
                                                    'text-sm font-medium',
                                                    transaction.type === 'add'
                                                        ? 'text-green-600'
                                                        : 'text-red-600',
                                                ]"
                                            >
                                                {{
                                                    transaction.type === 'add'
                                                        ? '+'
                                                        : '-'
                                                }}{{ parseFloat(transaction.quantity).toFixed(2) }}
                                            </span>
                                        </td>
                                        <td
                                            class="whitespace-nowrap px-6 py-4 text-sm text-gray-500"
                                        >
                                            {{ formatDate(transaction.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p v-else class="py-4 text-center text-gray-500">
                                No recent transactions.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
