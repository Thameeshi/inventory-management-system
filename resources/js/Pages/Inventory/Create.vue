<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    units: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    items: [
        {
            name: '',
            unit: 'pcs',
            quantity: '',
            note: '',
        },
    ],
});

const addRow = () => {
    form.items.push({
        name: '',
        unit: 'pcs',
        quantity: '',
        note: '',
    });
};

const removeRow = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('inventory.store'));
};
</script>

<template>
    <Head title="Add Items" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Add Items to Inventory
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
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">
                            <!-- Items List -->
                            <div
                                v-for="(item, index) in form.items"
                                :key="index"
                                class="rounded-lg border border-gray-200 bg-gray-50 p-4"
                            >
                                <div class="mb-4 flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-700">
                                        Item {{ index + 1 }}
                                    </h4>
                                    <button
                                        v-if="form.items.length > 1"
                                        type="button"
                                        @click="removeRow(index)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                                    <!-- Item Name -->
                                    <div class="sm:col-span-2">
                                        <InputLabel
                                            :for="'name-' + index"
                                            value="Item Name"
                                        />
                                        <TextInput
                                            :id="'name-' + index"
                                            v-model="item.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="e.g., Steel Rod"
                                            required
                                        />
                                        <InputError
                                            :message="form.errors[`items.${index}.name`]"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Unit -->
                                    <div>
                                        <InputLabel
                                            :for="'unit-' + index"
                                            value="Unit"
                                        />
                                        <select
                                            :id="'unit-' + index"
                                            v-model="item.unit"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required
                                        >
                                            <option
                                                v-for="unit in units"
                                                :key="unit"
                                                :value="unit"
                                            >
                                                {{ unit }}
                                            </option>
                                        </select>
                                        <InputError
                                            :message="form.errors[`items.${index}.unit`]"
                                            class="mt-2"
                                        />
                                    </div>

                                    <!-- Quantity -->
                                    <div>
                                        <InputLabel
                                            :for="'quantity-' + index"
                                            value="Quantity"
                                        />
                                        <TextInput
                                            :id="'quantity-' + index"
                                            v-model="item.quantity"
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            class="mt-1 block w-full"
                                            placeholder="0.00"
                                            required
                                        />
                                        <InputError
                                            :message="form.errors[`items.${index}.quantity`]"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="mt-4">
                                    <InputLabel
                                        :for="'note-' + index"
                                        value="Note (optional)"
                                    />
                                    <TextInput
                                        :id="'note-' + index"
                                        v-model="item.note"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="e.g., Received from supplier XYZ"
                                    />
                                    <InputError
                                        :message="form.errors[`items.${index}.note`]"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <!-- Add Another Item Button -->
                            <div class="flex justify-center">
                                <SecondaryButton type="button" @click="addRow">
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
                                    Add Another Item
                                </SecondaryButton>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end border-t border-gray-200 pt-4">
                                <PrimaryButton
                                    :disabled="form.processing"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    <span v-if="form.processing">Processing...</span>
                                    <span v-else>Add Items to Inventory</span>
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
