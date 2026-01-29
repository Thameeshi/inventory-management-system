<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    units: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: props.item.name,
    unit: props.item.unit,
    quantity: props.item.quantity,
    note: props.item.note || '',
});

const submit = () => {
    form.put(route('inventory.update', props.item.id));
};
</script>

<template>
    <Head :title="`Edit ${item.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('inventory.index')"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Item: {{ item.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Item Name -->
                        <div class="mb-6">
                            <InputLabel for="name" value="Item Name" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Enter item name"
                                autofocus
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Unit Selection -->
                        <div class="mb-6">
                            <InputLabel for="unit" value="Unit" />
                            <select
                                id="unit"
                                v-model="form.unit"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="" disabled>Select unit</option>
                                <option v-for="unit in units" :key="unit" :value="unit">
                                    {{ unit.toUpperCase() }}
                                </option>
                            </select>
                            <InputError :message="form.errors.unit" class="mt-2" />
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <InputLabel for="quantity" value="Quantity" />
                            <TextInput
                                id="quantity"
                                v-model="form.quantity"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full"
                                placeholder="Enter quantity"
                            />
                            <InputError :message="form.errors.quantity" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">
                                Current stock: {{ parseFloat(item.quantity).toFixed(2) }} {{ item.unit }}
                            </p>
                        </div>

                        <!-- Note -->
                        <div class="mb-6">
                            <InputLabel for="note" value="Note (Optional)" />
                            <textarea
                                id="note"
                                v-model="form.note"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Add a note about this item..."
                            ></textarea>
                            <InputError :message="form.errors.note" class="mt-2" />
                        </div>

                        <!-- Item Info -->
                        <div class="mb-6 rounded-md bg-gray-50 p-4">
                            <h4 class="text-sm font-medium text-gray-700">Item Information</h4>
                            <dl class="mt-2 divide-y divide-gray-200">
                                <div class="flex justify-between py-2">
                                    <dt class="text-sm text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ new Date(item.created_at).toLocaleDateString() }}
                                    </dd>
                                </div>
                                <div class="flex justify-between py-2">
                                    <dt class="text-sm text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ new Date(item.updated_at).toLocaleDateString() }}
                                    </dd>
                                </div>
                                <div class="flex justify-between py-2">
                                    <dt class="text-sm text-gray-500">Status</dt>
                                    <dd>
                                        <span
                                            :class="[
                                                'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                                item.status_color === 'red'
                                                    ? 'bg-red-100 text-red-800'
                                                    : item.status_color === 'yellow'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-green-100 text-green-800',
                                            ]"
                                        >
                                            {{ item.status_label }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('inventory.index')"
                                class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="-ml-1 mr-2 h-4 w-4 animate-spin"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    />
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    />
                                </svg>
                                Update Item
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="mt-6 overflow-hidden rounded-lg border border-red-200 bg-white shadow">
                    <div class="border-b border-red-200 bg-red-50 px-6 py-4">
                        <h3 class="text-lg font-medium text-red-800">Danger Zone</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Delete this item</h4>
                                <p class="text-sm text-gray-500">
                                    Once deleted, this item and all its transaction history will be permanently removed.
                                </p>
                            </div>
                            <Link
                                :href="route('inventory.destroy', item.id)"
                                method="delete"
                                as="button"
                                class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                                :onBefore="() => confirm('Are you sure you want to delete this item? This action cannot be undone.')"
                            >
                                Delete Item
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
