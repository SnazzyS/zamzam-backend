<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    trips: Array,
    categories: Array,
    preselectedTripId: [String, Number],
});

const form = useForm({
    trip_id: props.preselectedTripId || '',
    title: '',
    description: '',
    amount: '',
    currency: 'MVR',
    category: '',
    expense_date: new Date().toISOString().split('T')[0],
    fiscal_year: new Date().getFullYear(),
    payment_method: 'cash',
    transfer_reference_number: '',
    cheque_number: '',
    document: null,
    vendor_name: '',
});

const documentInput = ref(null);
const documentPreview = ref(null);
const isDragging = ref(false);

// Generate year options (current year and 5 years back)
const yearOptions = computed(() => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let i = 0; i <= 5; i++) {
        years.push(currentYear - i);
    }
    return years;
});

// Watch expense_date to auto-update fiscal_year when trip is not selected
watch(() => form.expense_date, (newDate) => {
    if (!form.trip_id && newDate) {
        form.fiscal_year = new Date(newDate).getFullYear();
    }
});

const handleFileSelect = (file) => {
    if (!file) return;
    const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    if (!validTypes.includes(file.type)) {
        alert('Please select a PDF or image file (JPG, PNG)');
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB');
        return;
    }
    form.document = file;
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            documentPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        documentPreview.value = null;
    }
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    const file = event.dataTransfer?.files?.[0];
    handleFileSelect(file);
};

const handleInputChange = (event) => {
    const file = event.target.files?.[0];
    handleFileSelect(file);
};

const clearDocument = () => {
    form.document = null;
    documentPreview.value = null;
    if (documentInput.value) {
        documentInput.value.value = '';
    }
};

const submitForm = () => {
    form.post(route('finance.expenses.store'), {
        forceFormData: true,
    });
};

const categoryLabels = {
    hotel: 'Hotel',
    transport: 'Transport',
    food: 'Food',
    visa: 'Visa',
    flights: 'Flights',
    other: 'Other',
};
</script>

<template>
    <Head title="Add Expense" />

    <main class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Add Expense</h1>
                <p class="text-sm text-slate-500">Create a new expense record</p>
            </div>
            <Link
                :href="route('finance.expenses.index')"
                class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
            >
                Cancel
            </Link>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="bg-white rounded-lg p-6 space-y-6">
            <!-- Trip Selection -->
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Trip (Optional)</label>
                    <select
                        v-model="form.trip_id"
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">General Expense (Not tied to a trip)</option>
                        <option v-for="trip in trips" :key="trip.id" :value="trip.id">{{ trip.name }}</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Leave empty for general business expenses</p>
                </div>

                <!-- Fiscal Year (shown when no trip selected) -->
                <div v-if="!form.trip_id">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Fiscal Year</label>
                    <select
                        v-model="form.fiscal_year"
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</option>
                    </select>
                </div>
            </div>

            <!-- Title and Amount -->
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Title *</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder="e.g., Hotel booking deposit"
                        required
                    >
                    <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount *</label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            placeholder="0.00"
                            required
                        >
                        <p v-if="form.errors.amount" class="text-xs text-red-500 mt-1">{{ form.errors.amount }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                        <select
                            v-model="form.currency"
                            class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="MVR">MVR</option>
                            <option value="USD">USD</option>
                            <option value="SAR">SAR</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Category and Date -->
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                    <select
                        v-model="form.category"
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">Select category</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ categoryLabels[cat] }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Expense Date *</label>
                    <input
                        v-model="form.expense_date"
                        type="date"
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        required
                    >
                    <p v-if="form.errors.expense_date" class="text-xs text-red-500 mt-1">{{ form.errors.expense_date }}</p>
                </div>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Payment Method *</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            v-model="form.payment_method"
                            value="cash"
                            class="text-blue-600 focus:ring-blue-500"
                        >
                        <span class="text-sm text-slate-700">Cash</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            v-model="form.payment_method"
                            value="transfer"
                            class="text-blue-600 focus:ring-blue-500"
                        >
                        <span class="text-sm text-slate-700">Bank Transfer</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            v-model="form.payment_method"
                            value="cheque"
                            class="text-blue-600 focus:ring-blue-500"
                        >
                        <span class="text-sm text-slate-700">Cheque</span>
                    </label>
                </div>
            </div>

            <!-- Transfer Reference Number (conditional) -->
            <div v-if="form.payment_method === 'transfer'">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Transfer Reference Number *</label>
                <input
                    v-model="form.transfer_reference_number"
                    type="text"
                    class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    placeholder="Enter transfer reference"
                    required
                >
                <p v-if="form.errors.transfer_reference_number" class="text-xs text-red-500 mt-1">{{ form.errors.transfer_reference_number }}</p>
            </div>

            <!-- Cheque Number (conditional) -->
            <div v-if="form.payment_method === 'cheque'">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Cheque Number *</label>
                <input
                    v-model="form.cheque_number"
                    type="text"
                    class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    placeholder="Enter cheque number"
                    required
                >
                <p v-if="form.errors.cheque_number" class="text-xs text-red-500 mt-1">{{ form.errors.cheque_number }}</p>
            </div>

            <!-- Vendor Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Vendor/Payee Name</label>
                <input
                    v-model="form.vendor_name"
                    type="text"
                    class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    placeholder="e.g., Al-Safwa Hotel"
                >
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    class="w-full rounded-md border border-slate-200 px-3 py-2 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    placeholder="Additional details about this expense"
                ></textarea>
            </div>

            <!-- Document Upload -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Invoice/Receipt Document</label>
                <div
                    class="relative border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer"
                    :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'"
                    @drop="handleDrop"
                    @dragover.prevent="isDragging = true"
                    @dragleave="isDragging = false"
                    @click="documentInput?.click()"
                >
                    <div v-if="form.document">
                        <div v-if="documentPreview" class="mb-3">
                            <img :src="documentPreview" alt="Preview" class="max-h-32 mx-auto rounded">
                        </div>
                        <div class="flex items-center justify-center gap-2 text-sm text-slate-600">
                            <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ form.document.name }}</span>
                            <button
                                type="button"
                                @click.stop="clearDocument"
                                class="ml-2 text-red-500 hover:text-red-700"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                    <div v-else>
                        <svg class="mx-auto h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="mt-2 text-sm text-slate-600">Drop file here or click to upload</p>
                        <p class="text-xs text-slate-400">PDF, JPG, PNG (max 10MB)</p>
                    </div>
                </div>
                <input
                    ref="documentInput"
                    type="file"
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="hidden"
                    @change="handleInputChange"
                >
                <p v-if="form.errors.document" class="text-xs text-red-500 mt-1">{{ form.errors.document }}</p>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <Link
                    :href="route('finance.expenses.index')"
                    class="rounded-md px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    class="rounded-md bg-blue-500 px-6 py-2 text-sm font-medium text-white transition hover:bg-blue-600 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Saving...' : 'Save Expense' }}
                </button>
            </div>
        </form>
    </main>
</template>
