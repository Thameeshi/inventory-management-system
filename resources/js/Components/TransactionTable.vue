<script setup>
defineProps({
    transactions: {
        type: Array,
        required: true,
    },
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                        Date
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
                        Note
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="transaction in transactions" :key="transaction.id">
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="text-sm text-gray-900">
                            {{ formatDate(transaction.created_at) }}
                        </div>
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
                            {{ transaction.type === 'add' ? 'Addition' : 'Deduction' }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div
                            :class="[
                                'text-sm font-medium',
                                transaction.type === 'add'
                                    ? 'text-green-600'
                                    : 'text-red-600',
                            ]"
                        >
                            {{ transaction.type === 'add' ? '+' : '-' }}{{ parseFloat(transaction.quantity).toFixed(2) }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500">
                            {{ transaction.note || '-' }}
                        </div>
                    </td>
                </tr>
                <tr v-if="transactions.length === 0">
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        No transactions found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
