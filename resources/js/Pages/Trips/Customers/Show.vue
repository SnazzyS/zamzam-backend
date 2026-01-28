<script setup>
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { tkGetChar, toDhivehi } from '../../../utils/lattinMapping';

const props = defineProps({
    trip: Object,
    customer: Object,
    tripCustomer: Object,
    invoice: Object,
    payments: Array,
    paymentSummary: Object,
    isStaff: Boolean,
});

const page = usePage();

// Watch for successful payment to open receipt
watch(() => page.props.flash?.payment_id, (paymentId) => {
    if (paymentId) {
        window.open(
            route('trips.customers.payments.show', [props.trip.id, props.customer.id, paymentId]),
            '_blank'
        );
    }
});

const showEditModal = ref(false);
const showPaymentModal = ref(false);
const showDiscountModal = ref(false);

const editForm = useForm({
    name: props.customer?.name ?? '',
    national_id: props.customer?.national_id ?? '',
    date_of_birth: props.customer?.date_of_birth ?? '',
    island: props.customer?.island ?? '',
    phone_number: props.customer?.phone_number ?? '',
    address: props.customer?.address ?? '',
    gender: props.customer?.gender ?? '',
    name_in_english: props.customer?.name_in_english ?? '',
    passport_number: props.customer?.passport_number ?? '',
    passport_issued_date: props.customer?.passport_issued_date ?? '',
    passport_expiry_date: props.customer?.passport_expiry_date ?? '',
    customer_type: props.tripCustomer?.customer_type ?? 'customer',
});

const paymentForm = useForm({
    amount: '',
    payment_method: 'cash',
    details: props.trip?.name ?? '',
    transfer_reference_number: '',
});

const discountForm = useForm({
    discount: '',
    reason: '',
});

const resetEditForm = () => {
    editForm.name = props.customer?.name ?? '';
    editForm.national_id = props.customer?.national_id ?? '';
    editForm.date_of_birth = props.customer?.date_of_birth ?? '';
    editForm.island = props.customer?.island ?? '';
    editForm.phone_number = props.customer?.phone_number ?? '';
    editForm.address = props.customer?.address ?? '';
    editForm.gender = props.customer?.gender ?? '';
    editForm.name_in_english = props.customer?.name_in_english ?? '';
    editForm.passport_number = props.customer?.passport_number ?? '';
    editForm.passport_issued_date = props.customer?.passport_issued_date ?? '';
    editForm.passport_expiry_date = props.customer?.passport_expiry_date ?? '';
    editForm.customer_type = props.tripCustomer?.customer_type ?? 'customer';
    editForm.clearErrors();
};

const openEditModal = () => {
    resetEditForm();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.clearErrors();
};

const submitEdit = () => {
    editForm.put(route('trips.customers.update', [props.trip.id, props.customer.id]), {
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const openPaymentModal = () => {
    paymentForm.reset();
    paymentForm.details = props.trip?.name ?? '';
    paymentForm.payment_method = 'cash';
    showPaymentModal.value = true;
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    paymentForm.clearErrors();
};

const submitPayment = () => {
    paymentForm.post(route('trips.customers.payments.store', [props.trip.id, props.customer.id]), {
        onSuccess: () => {
            closePaymentModal();
        },
    });
};

const openDiscountModal = () => {
    discountForm.reset();
    showDiscountModal.value = true;
};

const closeDiscountModal = () => {
    showDiscountModal.value = false;
    discountForm.clearErrors();
};

const submitDiscount = () => {
    discountForm.post(route('trips.customers.invoice.add-discount', [props.trip.id, props.customer.id]), {
        onSuccess: () => {
            closeDiscountModal();
        },
    });
};

const photoForm = useForm({
    photo: null,
    type: 'profile',
});

const photoInput = ref(null);
const isDragging = ref(false);
const photoPreview = ref(null);

const handleFileSelect = (file) => {
    if (!file || !file.type.startsWith('image/')) {
        return;
    }
    photoForm.photo = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    const file = event.dataTransfer?.files?.[0];
    handleFileSelect(file);
};

const handleDragOver = (event) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const handleInputChange = (event) => {
    const file = event.target.files?.[0];
    handleFileSelect(file);
};

const clearPhotoPreview = () => {
    photoForm.photo = null;
    photoPreview.value = null;
    if (photoInput.value) {
        photoInput.value.value = '';
    }
};

const submitPhoto = () => {
    photoForm.post(route('trips.customers.photos.store', [props.trip.id, props.customer.id]), {
        forceFormData: true,
        onSuccess: () => {
            photoForm.reset('photo');
            photoPreview.value = null;
            if (photoInput.value) {
                photoInput.value.value = '';
            }
        },
    });
};

const profilePhoto = computed(() => {
    return props.customer?.photos?.find(p => p.type === 'profile');
});

const handleDhivehiInput = (event, form, field) => {
    const mapped = toDhivehi(event.target.value);
    if (mapped !== event.target.value) {
        event.target.value = mapped;
    }
    form[field] = mapped;
};

const handleDhivehiKeydown = (event, form, field) => {
    if (event.isComposing || event.ctrlKey || event.metaKey || event.altKey) {
        return;
    }

    const key = event.key;
    if (key.length !== 1) {
        return;
    }

    const mapped = tkGetChar(key);
    if (mapped === key) {
        return;
    }

    event.preventDefault();
    const input = event.target;
    const start = input.selectionStart ?? 0;
    const end = input.selectionEnd ?? 0;
    const nextValue = `${input.value.slice(0, start)}${mapped}${input.value.slice(end)}`;
    input.value = nextValue;
    const nextPos = start + mapped.length;
    input.setSelectionRange(nextPos, nextPos);
    form[field] = nextValue;
};

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    if (typeof value === 'string' && value.includes('-')) {
        const [year, month, day] = value.split('-');
        if (day) {
            return `${day}/${month}/${year}`;
        }
    }

    return value;
};

const formatDateTime = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const openReceipt = (paymentId) => {
    window.open(
        route('trips.customers.payments.show', [props.trip.id, props.customer.id, paymentId]),
        '_blank'
    );
};
</script>

<template>
    <Head title="Customer" />

    <main class="space-y-6">
        <!-- Header with buttons on left -->
        <div class="flex items-center gap-2">
            <Link
                :href="route('trips.show', trip.id)"
                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
            >
                Back to Trip
            </Link>
            <button
                type="button"
                class="rounded-lg bg-violet-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-violet-700"
                @click="openEditModal"
            >
                Edit Customer
            </button>
        </div>

        <!-- Profile header with photo and hajee number -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col items-center gap-4">
                <!-- Circular profile photo with drag-and-drop -->
                <div
                    class="relative w-32 h-32 rounded-full border-2 border-dashed transition-colors cursor-pointer overflow-hidden"
                    :class="isDragging ? 'border-violet-500 bg-violet-50' : profilePhoto ? 'border-transparent' : 'border-slate-300 hover:border-slate-400'"
                    @drop="handleDrop"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    @click="photoInput?.click()"
                >
                    <!-- Current photo or preview -->
                    <img
                        v-if="photoPreview"
                        :src="photoPreview"
                        :alt="customer?.name"
                        class="w-full h-full object-cover"
                    >
                    <img
                        v-else-if="profilePhoto"
                        :src="`/storage/${profilePhoto.path}`"
                        :alt="customer?.name"
                        class="w-full h-full object-cover"
                    >
                    <!-- Placeholder when no photo -->
                    <div v-else class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span class="text-xs mt-1">Drop photo</span>
                    </div>
                    <!-- Clear button when there's a preview -->
                    <button
                        v-if="photoPreview"
                        type="button"
                        class="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow-sm hover:bg-red-600"
                        @click.stop="clearPhotoPreview"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <input
                    ref="photoInput"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    @change="handleInputChange"
                >

                <!-- Upload button when preview is ready -->
                <button
                    v-if="photoPreview"
                    type="button"
                    class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                    :disabled="photoForm.processing"
                    @click="submitPhoto"
                >
                    {{ photoForm.processing ? 'Uploading...' : 'Upload Photo' }}
                </button>
                <p v-if="photoForm.errors.photo" class="text-xs text-red-500">{{ photoForm.errors.photo }}</p>

                <!-- Staff badge -->
                <div v-if="isStaff" class="mb-2">
                    <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-sm font-medium text-orange-700" dir="rtl" lang="dv">
                        ސްޓާފް
                    </span>
                </div>

                <!-- Hajee number -->
                <div class="text-center">
                    <p class="text-xs text-slate-500 uppercase tracking-wide">Hajee Number</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ tripCustomer?.umrah_id || '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Summary Card (hidden for staff) -->
        <div v-if="!isStaff" class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Payment Summary</h2>
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700 transition hover:bg-amber-100"
                        @click="openDiscountModal"
                    >
                        Add Discount
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-emerald-700"
                        @click="openPaymentModal"
                    >
                        Accept Payment
                    </button>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 rounded-lg bg-slate-50">
                        <p class="text-xs text-slate-500 mb-1">Trip Price</p>
                        <p class="text-lg font-bold text-slate-800">{{ formatCurrency(paymentSummary?.tripPrice) }}</p>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-amber-50">
                        <p class="text-xs text-amber-600 mb-1">Discount</p>
                        <p class="text-lg font-bold text-amber-700">{{ formatCurrency(paymentSummary?.discount) }}</p>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-emerald-50">
                        <p class="text-xs text-emerald-600 mb-1">Total Paid</p>
                        <p class="text-lg font-bold text-emerald-700">{{ formatCurrency(paymentSummary?.totalPaid) }}</p>
                    </div>
                    <div class="text-center p-4 rounded-lg" :class="paymentSummary?.balance > 0 ? 'bg-red-50' : 'bg-emerald-50'">
                        <p class="text-xs mb-1" :class="paymentSummary?.balance > 0 ? 'text-red-600' : 'text-emerald-600'">Balance</p>
                        <p class="text-lg font-bold" :class="paymentSummary?.balance > 0 ? 'text-red-700' : 'text-emerald-700'">{{ formatCurrency(paymentSummary?.balance) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History (hidden for staff) -->
        <div v-if="!isStaff" class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-700">Payment History</h2>
            </div>
            <div v-if="payments && payments.length" class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                        <tr>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Receipt #</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Date</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Amount</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Method</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-left">Details</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        <tr
                            v-for="payment in payments"
                            :key="payment.id"
                            class="even:bg-slate-50/50 hover:bg-violet-50/50 transition-colors"
                        >
                            <td class="border-b border-slate-100 px-4 py-3 font-mono text-slate-600">#{{ payment.id }}</td>
                            <td class="border-b border-slate-100 px-4 py-3">{{ formatDateTime(payment.created_at) }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 font-medium text-emerald-600">{{ formatCurrency(payment.amount) }}</td>
                            <td class="border-b border-slate-100 px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium" :class="{
                                    'bg-emerald-100 text-emerald-700': payment.payment_method === 'cash',
                                    'bg-blue-100 text-blue-700': payment.payment_method === 'transfer',
                                }">
                                    {{ payment.payment_method === 'cash' ? 'Cash' : 'Transfer' }}
                                </span>
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3 text-slate-500">{{ payment.details || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 text-center">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                                    @click="openReceipt(payment.id)"
                                    title="View Receipt"
                                >
                                    <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Receipt
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-8 text-center text-slate-500">
                <p class="text-sm">No payments recorded yet</p>
            </div>
        </div>

        <!-- Two column layout: Passport (left) | Customer details in Dhivehi (right) -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- LEFT: Passport details in English -->
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-700">Passport Details</h2>
                <dl class="mt-4 space-y-4">
                    <div>
                        <dt class="text-xs text-slate-500">Name in Passport</dt>
                        <dd class="mt-1 text-sm text-slate-900" dir="ltr">{{ customer?.name_in_english || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Passport Number</dt>
                        <dd class="mt-1 text-sm font-mono text-slate-900" dir="ltr">{{ customer?.passport_number || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Passport Issue Date</dt>
                        <dd class="mt-1 text-sm text-slate-900" dir="ltr">{{ formatDate(customer?.passport_issued_date) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Passport Expiry Date</dt>
                        <dd class="mt-1 text-sm text-slate-900" dir="ltr">{{ formatDate(customer?.passport_expiry_date) }}</dd>
                    </div>
                </dl>
            </section>

            <!-- RIGHT: Customer details in Dhivehi -->
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" dir="rtl" lang="dv">
                <h2 class="text-base font-semibold text-slate-700">ކަސްޓަމަރުގެ މައުލޫމާތު</h2>
                <dl class="mt-4 space-y-4">
                    <div>
                        <dt class="text-sm text-slate-500">ނަން</dt>
                        <dd class="mt-1 text-base text-slate-900">{{ customer?.name || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">އައިޑީ ކާޑު ނަންބަރު</dt>
                        <dd class="mt-1 text-base font-mono text-slate-900" dir="ltr">{{ customer?.national_id || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">އެޑްރެސް</dt>
                        <dd class="mt-1 text-base text-slate-900">{{ customer?.address || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">ރަށް</dt>
                        <dd class="mt-1 text-base text-slate-900">{{ customer?.island || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">ޖިންސު</dt>
                        <dd class="mt-1 text-base text-slate-900">{{ customer?.gender === 'Male' ? 'ފިރިހެން' : customer?.gender === 'Female' ? 'އަންހެން' : '-' }}</dd>
                    </div>
                </dl>
            </section>
        </div>
    </main>

    <Teleport to="body">
        <!-- Edit Customer Modal -->
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showEditModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeEditModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showEditModal" class="relative w-full max-w-3xl rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Edit Customer</h3>
                                    <p class="text-sm text-slate-500">Update customer details</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeEditModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitEdit" class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                                    <input
                                        :value="editForm.name"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        @keydown="handleDhivehiKeydown($event, editForm, 'name')"
                                        @input="handleDhivehiInput($event, editForm, 'name')"
                                        required
                                    >
                                    <p v-if="editForm.errors.name" class="text-xs text-red-500 mt-1">{{ editForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">National ID</label>
                                    <input
                                        v-model="editForm.national_id"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                    <p v-if="editForm.errors.national_id" class="text-xs text-red-500 mt-1">{{ editForm.errors.national_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth</label>
                                    <input
                                        v-model="editForm.date_of_birth"
                                        type="date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                    <p v-if="editForm.errors.date_of_birth" class="text-xs text-red-500 mt-1">{{ editForm.errors.date_of_birth }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Island</label>
                                    <input
                                        :value="editForm.island"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        @keydown="handleDhivehiKeydown($event, editForm, 'island')"
                                        @input="handleDhivehiInput($event, editForm, 'island')"
                                        required
                                    >
                                    <p v-if="editForm.errors.island" class="text-xs text-red-500 mt-1">{{ editForm.errors.island }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number</label>
                                    <input
                                        v-model="editForm.phone_number"
                                        type="number"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                    <p v-if="editForm.errors.phone_number" class="text-xs text-red-500 mt-1">{{ editForm.errors.phone_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                                    <input
                                        :value="editForm.address"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        @keydown="handleDhivehiKeydown($event, editForm, 'address')"
                                        @input="handleDhivehiInput($event, editForm, 'address')"
                                        required
                                    >
                                    <p v-if="editForm.errors.address" class="text-xs text-red-500 mt-1">{{ editForm.errors.address }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender</label>
                                    <select
                                        v-model="editForm.gender"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                        <option value="" disabled>Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <p v-if="editForm.errors.gender" class="text-xs text-red-500 mt-1">{{ editForm.errors.gender }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Name in English</label>
                                    <input
                                        v-model="editForm.name_in_english"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                    >
                                    <p v-if="editForm.errors.name_in_english" class="text-xs text-red-500 mt-1">{{ editForm.errors.name_in_english }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Passport Number</label>
                                    <input
                                        v-model="editForm.passport_number"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                    >
                                    <p v-if="editForm.errors.passport_number" class="text-xs text-red-500 mt-1">{{ editForm.errors.passport_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Passport Issued Date</label>
                                    <input
                                        v-model="editForm.passport_issued_date"
                                        type="date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                    >
                                    <p v-if="editForm.errors.passport_issued_date" class="text-xs text-red-500 mt-1">{{ editForm.errors.passport_issued_date }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Passport Expiry Date</label>
                                    <input
                                        v-model="editForm.passport_expiry_date"
                                        type="date"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                    >
                                    <p v-if="editForm.errors.passport_expiry_date" class="text-xs text-red-500 mt-1">{{ editForm.errors.passport_expiry_date }}</p>
                                </div>

                                <!-- Customer Type -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5" dir="rtl" lang="dv">ބާވަތް</label>
                                    <div class="flex gap-6" dir="rtl">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                v-model="editForm.customer_type"
                                                value="customer"
                                                class="text-violet-600 focus:ring-violet-500"
                                            >
                                            <span class="text-sm text-slate-700" lang="dv">ކަސްޓަމަރު</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                v-model="editForm.customer_type"
                                                value="staff"
                                                class="text-violet-600 focus:ring-violet-500"
                                            >
                                            <span class="text-sm text-slate-700" lang="dv">ސްޓާފް</span>
                                        </label>
                                    </div>
                                    <p v-if="editForm.customer_type === 'staff'" class="text-xs text-orange-600 mt-1" dir="rtl" lang="dv">ސްޓާފުންނަށް ޕޭމަންޓް ބޭނުމެއް ނުވާނެ</p>
                                </div>

                                <div class="md:col-span-2 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeEditModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                                        :disabled="editForm.processing"
                                    >
                                        {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>

        <!-- Payment Modal -->
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showPaymentModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closePaymentModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showPaymentModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Accept Payment</h3>
                                    <p class="text-sm text-slate-500">Balance: <span class="font-medium text-red-600">{{ formatCurrency(paymentSummary?.balance) }}</span></p>
                                </div>
                                <button
                                    type="button"
                                    @click="closePaymentModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitPayment" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount</label>
                                    <input
                                        v-model="paymentForm.amount"
                                        type="number"
                                        step="0.01"
                                        min="1"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        placeholder="Enter amount"
                                        required
                                    >
                                    <p v-if="paymentForm.errors.amount" class="text-xs text-red-500 mt-1">{{ paymentForm.errors.amount }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Payment Method</label>
                                    <select
                                        v-model="paymentForm.payment_method"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Bank Transfer</option>
                                    </select>
                                    <p v-if="paymentForm.errors.payment_method" class="text-xs text-red-500 mt-1">{{ paymentForm.errors.payment_method }}</p>
                                </div>

                                <div v-if="paymentForm.payment_method === 'transfer'">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Transfer Reference</label>
                                    <input
                                        v-model="paymentForm.transfer_reference_number"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        placeholder="Reference number"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Reason for Payment</label>
                                    <input
                                        v-model="paymentForm.details"
                                        type="text"
                                        dir="rtl"
                                        lang="dv"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        :placeholder="trip?.name"
                                    >
                                    <p class="text-xs text-slate-500 mt-1">Default: Trip name</p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closePaymentModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:opacity-50"
                                        :disabled="paymentForm.processing"
                                    >
                                        {{ paymentForm.processing ? 'Processing...' : 'Accept Payment' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>

        <!-- Discount Modal -->
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showDiscountModal" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeDiscountModal"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="showDiscountModal" class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Add Discount</h3>
                                    <p class="text-sm text-slate-500">Current discount: <span class="font-medium text-amber-600">{{ formatCurrency(paymentSummary?.discount) }}</span></p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeDiscountModal"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="submitDiscount" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Discount Amount</label>
                                    <input
                                        v-model="discountForm.discount"
                                        type="number"
                                        step="0.01"
                                        min="1"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        placeholder="Enter discount amount"
                                        required
                                    >
                                    <p v-if="discountForm.errors.discount" class="text-xs text-red-500 mt-1">{{ discountForm.errors.discount }}</p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button
                                        type="button"
                                        @click="closeDiscountModal"
                                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 disabled:opacity-50"
                                        :disabled="discountForm.processing"
                                    >
                                        {{ discountForm.processing ? 'Applying...' : 'Apply Discount' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
