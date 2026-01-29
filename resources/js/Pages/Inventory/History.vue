<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TransactionTable from '@/Components/TransactionTable.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    item: {
        type: Object,
        required: true,
    },
    transactions: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head :title="`History - ${item.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Item History
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Transaction history for
                        <span class="font-medium">{{ item.name }}</span>
                    </p>
                </div>
                <Link
                    :href="route('inventory.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    &larr; Back to Inventory
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Item Summary Card -->
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Item Name
                                </dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ item.name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Current Quantity
                                </dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ parseFloat(item.quantity).toFixed(2) }}
                                    <span class="text-sm font-normal text-gray-500">
                                        {{ item.unit }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold leading-5',
                                            parseFloat(item.quantity) < 10
                                                ? 'bg-red-100 text-red-800'
                                                : 'bg-green-100 text-green-800',
                                        ]"
                                    >
                                        {{
                                            parseFloat(item.quantity) < 10
                                                ? 'Low Stock'
                                                : 'In Stock'
                                        }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Total Transactions
                                </dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ transactions.length }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Transaction History
                        </h3>
                    </div>
                    <TransactionTable :transactions="transactions" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
