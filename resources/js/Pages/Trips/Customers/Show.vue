<script setup>
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { tkGetChar, toDhivehi } from '../../../utils/lattinMapping';
import { searchIslands, getIslandOptions } from '../../../utils/maldivesIslands';
import { searchCountries, getCountryOptions } from '../../../utils/countries';

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

// Determine if customer is foreigner based on existing data
const isForeigner = ref(props.customer?.is_foreigner ?? false);

// Island dropdown state
const showIslandDropdown = ref(false);
const islandSearch = ref('');
const filteredIslands = computed(() => searchIslands(islandSearch.value));

// Country dropdown state
const showCountryDropdown = ref(false);
const countrySearch = ref('');
const filteredCountries = computed(() => searchCountries(countrySearch.value));

// Get display label for selected island
const selectedIslandLabel = computed(() => {
    if (!editForm.island) return '';
    const islands = getIslandOptions();
    const found = islands.find(i => i.value === editForm.island);
    return found ? found.label : editForm.island;
});

// Get display label for selected country
const selectedCountryLabel = computed(() => {
    if (!editForm.country) return '';
    const countries = getCountryOptions();
    const found = countries.find(c => c.value === editForm.country);
    return found ? found.label : editForm.country;
});

const selectIsland = (island) => {
    editForm.island = island.value;
    showIslandDropdown.value = false;
    islandSearch.value = '';
};

const selectCountry = (country) => {
    editForm.country = country.value;
    showCountryDropdown.value = false;
    countrySearch.value = '';
};

// Watch for foreigner toggle changes
watch(isForeigner, (newValue) => {
    editForm.is_foreigner = newValue;
    if (newValue) {
        // Switching to foreigner - clear island
        editForm.island = '';
    } else {
        // Switching to local - clear country
        editForm.country = '';
    }
});

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
    is_foreigner: props.customer?.is_foreigner ?? false,
    country: props.customer?.country ?? '',
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
    editForm.is_foreigner = props.customer?.is_foreigner ?? false;
    editForm.country = props.customer?.country ?? '';
    isForeigner.value = props.customer?.is_foreigner ?? false;
    islandSearch.value = '';
    countrySearch.value = '';
    showIslandDropdown.value = false;
    showCountryDropdown.value = false;
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

// Visa form
const visaForm = useForm({
    visa: null,
});

const visaInput = ref(null);

const handleVisaSelect = (event) => {
    const file = event.target.files?.[0];
    if (file && file.type === 'application/pdf') {
        visaForm.visa = file;
    }
};

const submitVisa = () => {
    visaForm.post(route('trips.customers.visa.upload', [props.trip.id, props.customer.id]), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            visaForm.reset('visa');
            if (visaInput.value) {
                visaInput.value.value = '';
            }
        },
    });
};

const removeVisa = () => {
    if (!confirm('Are you sure you want to remove the visa?')) return;
    router.delete(route('trips.customers.visa.remove', [props.trip.id, props.customer.id]), {
        preserveScroll: true,
    });
};

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

const deletePhoto = (photoId) => {
    if (!confirm('Are you sure you want to remove this photo?')) return;
    router.delete(route('trips.customers.photos.destroy', [props.trip.id, props.customer.id, photoId]), {
        preserveScroll: true,
    });
};

const showPhotoActions = ref(false);
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
                    class="relative w-32 h-32 rounded-full border-2 border-dashed transition-colors cursor-pointer overflow-hidden group"
                    :class="isDragging ? 'border-violet-500 bg-violet-50' : profilePhoto ? 'border-transparent' : 'border-slate-300 hover:border-slate-400'"
                    @drop="handleDrop"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    @click="photoInput?.click()"
                    @mouseenter="showPhotoActions = true"
                    @mouseleave="showPhotoActions = false"
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
                    <!-- Hover overlay with actions for existing photo -->
                    <div
                        v-if="profilePhoto && !photoPreview && showPhotoActions"
                        class="absolute inset-0 bg-black/50 flex items-center justify-center gap-2 rounded-full"
                        @click.stop
                    >
                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm hover:bg-slate-100"
                            @click.stop="photoInput?.click()"
                            title="Replace photo"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-sm hover:bg-red-600"
                            @click.stop="deletePhoto(profilePhoto.id)"
                            title="Remove photo"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
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
                    {{ photoForm.processing ? 'Uploading...' : profilePhoto ? 'Replace Photo' : 'Upload Photo' }}
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

                <!-- Visa Upload Section -->
                <div class="mt-6 pt-6 border-t border-slate-200 w-full">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Visa Document</h3>

                    <!-- Current Visa -->
                    <div v-if="tripCustomer?.visa_path" class="flex items-center justify-between bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-emerald-700">Visa Uploaded</p>
                                <a :href="'/storage/' + tripCustomer.visa_path" target="_blank" class="text-xs text-emerald-600 hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View PDF
                                </a>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="flex items-center gap-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition text-sm"
                            @click="removeVisa"
                            title="Remove Visa"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Remove
                        </button>
                    </div>

                    <!-- No visa message -->
                    <div v-else class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 mb-3">
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-slate-500">No visa uploaded yet</p>
                    </div>

                    <!-- Upload New Visa -->
                    <div class="flex items-center gap-3">
                        <input
                            ref="visaInput"
                            type="file"
                            accept="application/pdf"
                            class="flex-1 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 cursor-pointer"
                            @change="handleVisaSelect"
                        >
                        <button
                            v-if="visaForm.visa"
                            type="button"
                            class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50 flex items-center gap-2"
                            :disabled="visaForm.processing"
                            @click="submitVisa"
                        >
                            <svg v-if="visaForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            {{ visaForm.processing ? 'Uploading...' : (tripCustomer?.visa_path ? 'Replace Visa' : 'Upload Visa') }}
                        </button>
                    </div>
                    <p v-if="visaForm.errors.visa" class="text-xs text-red-500 mt-1">{{ visaForm.errors.visa }}</p>
                    <p class="text-xs text-slate-400 mt-2">Accepted format: PDF (max 10MB)</p>
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
                    <!-- National ID for locals -->
                    <div v-if="!customer?.is_foreigner">
                        <dt class="text-sm text-slate-500">އައިޑީ ކާޑު ނަންބަރު</dt>
                        <dd class="mt-1 text-base font-mono text-slate-900" dir="ltr">{{ customer?.national_id || '-' }}</dd>
                    </div>
                    <!-- Country for foreigners -->
                    <div v-if="customer?.is_foreigner">
                        <dt class="text-sm text-slate-500">ޤައުމު</dt>
                        <dd class="mt-1 text-base text-slate-900" dir="ltr">{{ customer?.country || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">އެޑްރެސް</dt>
                        <dd class="mt-1 text-base text-slate-900">{{ customer?.address || '-' }}</dd>
                    </div>
                    <!-- Island for locals -->
                    <div v-if="!customer?.is_foreigner">
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
                                <!-- Local/Foreigner Toggle -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Customer Type</label>
                                    <div class="flex items-center gap-2 rounded-lg bg-slate-100 p-1 w-fit">
                                        <button
                                            type="button"
                                            @click="isForeigner = false"
                                            class="rounded-md px-4 py-1.5 text-sm font-medium transition-all"
                                            :class="!isForeigner ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                                        >
                                            Local
                                        </button>
                                        <button
                                            type="button"
                                            @click="isForeigner = true"
                                            class="rounded-md px-4 py-1.5 text-sm font-medium transition-all"
                                            :class="isForeigner ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                                        >
                                            Foreigner
                                        </button>
                                    </div>
                                </div>

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
                                <!-- National ID (for locals) -->
                                <div v-if="!isForeigner">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">National ID</label>
                                    <input
                                        v-model="editForm.national_id"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                    <p v-if="editForm.errors.national_id" class="text-xs text-red-500 mt-1">{{ editForm.errors.national_id }}</p>
                                </div>
                                <!-- Passport Number (for foreigners - moved here) -->
                                <div v-if="isForeigner">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Passport Number</label>
                                    <input
                                        v-model="editForm.passport_number"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                    <p v-if="editForm.errors.passport_number" class="text-xs text-red-500 mt-1">{{ editForm.errors.passport_number }}</p>
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
                                <!-- Island dropdown (for locals) -->
                                <div v-if="!isForeigner" class="relative">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Island</label>
                                    <button
                                        type="button"
                                        @click="showIslandDropdown = !showIslandDropdown"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-right bg-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        dir="rtl"
                                        lang="dv"
                                    >
                                        <span :class="editForm.island ? 'text-slate-900' : 'text-slate-400'">
                                            {{ selectedIslandLabel || 'ރަށް އިޚްތިޔާރުކުރައްވާ' }}
                                        </span>
                                    </button>
                                    <!-- Island dropdown list -->
                                    <div
                                        v-if="showIslandDropdown"
                                        class="absolute z-50 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg max-h-60 overflow-hidden"
                                    >
                                        <div class="p-2 border-b border-slate-100">
                                            <input
                                                v-model="islandSearch"
                                                type="text"
                                                dir="rtl"
                                                lang="dv"
                                                class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-sm focus:border-violet-500 focus:outline-none"
                                                placeholder="ރަށް ހޯދާ..."
                                                @click.stop
                                            >
                                        </div>
                                        <div class="max-h-48 overflow-y-auto">
                                            <button
                                                v-for="island in filteredIslands"
                                                :key="island.value"
                                                type="button"
                                                @click="selectIsland(island)"
                                                class="w-full px-3 py-2 text-right text-sm hover:bg-violet-50 transition-colors"
                                                dir="rtl"
                                                lang="dv"
                                                :class="editForm.island === island.value ? 'bg-violet-100 text-violet-700' : 'text-slate-700'"
                                            >
                                                {{ island.label }}
                                            </button>
                                        </div>
                                    </div>
                                    <p v-if="editForm.errors.island" class="text-xs text-red-500 mt-1">{{ editForm.errors.island }}</p>
                                </div>
                                <!-- Country dropdown (for foreigners) -->
                                <div v-if="isForeigner" class="relative">
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Country</label>
                                    <button
                                        type="button"
                                        @click="showCountryDropdown = !showCountryDropdown"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-left bg-white focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                    >
                                        <span :class="editForm.country ? 'text-slate-900' : 'text-slate-400'">
                                            {{ selectedCountryLabel || 'Select country' }}
                                        </span>
                                    </button>
                                    <!-- Country dropdown list -->
                                    <div
                                        v-if="showCountryDropdown"
                                        class="absolute z-50 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg max-h-60 overflow-hidden"
                                    >
                                        <div class="p-2 border-b border-slate-100">
                                            <input
                                                v-model="countrySearch"
                                                type="text"
                                                class="w-full rounded-md border border-slate-200 px-3 py-1.5 text-sm focus:border-violet-500 focus:outline-none"
                                                placeholder="Search country..."
                                                @click.stop
                                            >
                                        </div>
                                        <div class="max-h-48 overflow-y-auto">
                                            <button
                                                v-for="country in filteredCountries"
                                                :key="country.value"
                                                type="button"
                                                @click="selectCountry(country)"
                                                class="w-full px-3 py-2 text-left text-sm hover:bg-violet-50 transition-colors"
                                                :class="editForm.country === country.value ? 'bg-violet-100 text-violet-700' : 'text-slate-700'"
                                            >
                                                {{ country.label }}
                                            </button>
                                        </div>
                                    </div>
                                    <p v-if="editForm.errors.country" class="text-xs text-red-500 mt-1">{{ editForm.errors.country }}</p>
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
                                <!-- Passport Number for locals (foreigners have it at the top) -->
                                <div v-if="!isForeigner">
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
