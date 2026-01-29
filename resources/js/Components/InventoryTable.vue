<script setup>
defineProps({
    items: {
        type: Array,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                        Name
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                        Quantity
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                        Unit
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                        Status
                    </th>
                    <th
                        v-if="showActions"
                        class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="item in items" :key="item.id">
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            {{ item.name }}
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="text-sm text-gray-900">
                            {{ parseFloat(item.quantity).toFixed(2) }}
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="text-sm text-gray-500">
                            {{ item.unit }}
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span
                            :class="[
                                'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                parseFloat(item.quantity) < 10
                                    ? 'bg-red-100 text-red-800'
                                    : 'bg-green-100 text-green-800',
                            ]"
                        >
                            {{ parseFloat(item.quantity) < 10 ? 'Low Stock' : 'In Stock' }}
                        </span>
                    </td>
                    <td
                        v-if="showActions"
                        class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium"
                    >
                        <slot name="actions" :item="item" />
                    </td>
                </tr>
                <tr v-if="items.length === 0">
                    <td
                        :colspan="showActions ? 5 : 4"
                        class="px-6 py-8 text-center text-gray-500"
                    >
                        No items found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
