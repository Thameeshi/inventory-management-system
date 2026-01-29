<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    deductions: [],
});

const toggleItem = (item) => {
    const existingIndex = form.deductions.findIndex(
        (d) => d.item_id === item.id
    );

    if (existingIndex !== -1) {
        form.deductions.splice(existingIndex, 1);
    } else {
        form.deductions.push({
            item_id: item.id,
            quantity: '',
            note: '',
        });
    }
};

const isSelected = (itemId) => {
    return form.deductions.some((d) => d.item_id === itemId);
};

const getDeduction = (itemId) => {
    return form.deductions.find((d) => d.item_id === itemId);
};

const getDeductionIndex = (itemId) => {
    return form.deductions.findIndex((d) => d.item_id === itemId);
};

const selectedCount = computed(() => form.deductions.length);

const submit = () => {
    form.post(route('inventory.deduct.store'));
};
</script>

<template>
    <Head title="Deduct Items" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Deduct Items from Inventory
                </h2>
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
                <!-- Error Messages -->
                <div
                    v-if="form.errors.error"
                    class="mb-4 rounded-md bg-red-50 p-4"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-red-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ form.errors.error }}
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    <div class="overflow-hidden rounded-lg bg-white shadow">
                        <div class="p-6">
                            <p class="mb-4 text-sm text-gray-600">
                                Select items to deduct from inventory. You can
                                deduct multiple items at once.
                            </p>

                            <!-- Items Table -->
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200"
                                >
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="w-12 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Select
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Item
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Available
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Deduct Qty
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                            >
                                                Note
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-gray-200 bg-white"
                                    >
                                        <tr
                                            v-for="item in items"
                                            :key="item.id"
                                            :class="[
                                                isSelected(item.id)
                                                    ? 'bg-blue-50'
                                                    : '',
                                            ]"
                                        >
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <input
                                                    type="checkbox"
                                                    :checked="isSelected(item.id)"
                                                    @change="toggleItem(item)"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    :disabled="parseFloat(item.quantity) <= 0"
                                                />
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ item.name }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span
                                                    :class="[
                                                        'text-sm',
                                                        parseFloat(item.quantity) < 10
                                                            ? 'text-red-600 font-medium'
                                                            : 'text-gray-900',
                                                    ]"
                                                >
                                                    {{ parseFloat(item.quantity).toFixed(2) }}
                                                    {{ item.unit }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <div v-if="isSelected(item.id)">
                                                    <TextInput
                                                        v-model="getDeduction(item.id).quantity"
                                                        type="number"
                                                        step="0.01"
                                                        min="0.01"
                                                        :max="item.quantity"
                                                        class="w-24"
                                                        placeholder="0.00"
                                                        required
                                                    />
                                                    <InputError
                                                        :message="
                                                            form.errors[
                                                                `deductions.${getDeductionIndex(item.id)}.quantity`
                                                            ]
                                                        "
                                                        class="mt-1"
                                                    />
                                                </div>
                                                <span v-else class="text-gray-400">
                                                    -
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div v-if="isSelected(item.id)">
                                                    <TextInput
                                                        v-model="getDeduction(item.id).note"
                                                        type="text"
                                                        class="w-full max-w-xs"
                                                        placeholder="Optional note"
                                                    />
                                                </div>
                                                <span v-else class="text-gray-400">
                                                    -
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="items.length === 0">
                                            <td
                                                colspan="5"
                                                class="px-6 py-8 text-center text-gray-500"
                                            >
                                                No items with available stock.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4"
                        >
                            <p class="text-sm text-gray-600">
                                {{ selectedCount }} item(s) selected
                            </p>
                            <PrimaryButton
                                :disabled="form.processing || selectedCount === 0"
                                :class="{
                                    'opacity-25':
                                        form.processing || selectedCount === 0,
                                }"
                            >
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Deduct Selected Items</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
