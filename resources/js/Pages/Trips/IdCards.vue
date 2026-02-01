<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
    trip: Object,
    customers: Array,
    primaryHotel: Object,
    companySettings: Object,
});

// Selection state - by default all customers are selected
const selectedIds = ref(new Set(props.customers.map(c => c.id)));
const showSelectionPanel = ref(false);

// Check URL for pre-selected customers
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const customersParam = urlParams.get('customers');
    if (customersParam) {
        const ids = customersParam.split(',').map(id => parseInt(id)).filter(id => !isNaN(id));
        selectedIds.value = new Set(ids);
    }
});

// Filtered customers based on selection
const filteredCustomers = computed(() => {
    return props.customers.filter(c => selectedIds.value.has(c.id));
});

// Split customers into pages (4 cards per page: 2 columns x 2 rows on landscape A4)
const cardsPerPage = 4;
const pages = computed(() => {
    const result = [];
    for (let i = 0; i < filteredCustomers.value.length; i += cardsPerPage) {
        result.push(filteredCustomers.value.slice(i, i + cardsPerPage));
    }
    return result;
});

const toggleCustomer = (customerId) => {
    const newSet = new Set(selectedIds.value);
    if (newSet.has(customerId)) {
        newSet.delete(customerId);
    } else {
        newSet.add(customerId);
    }
    selectedIds.value = newSet;
};

const selectAll = () => {
    selectedIds.value = new Set(props.customers.map(c => c.id));
};

const deselectAll = () => {
    selectedIds.value = new Set();
};

const printCards = () => {
    window.print();
};

// Generate QR code URL for a customer
const getQrUrl = (customer) => {
    return `${window.location.origin}/card/${props.trip.id}/${customer.id}`;
};
</script>

<template>
    <Head :title="`${trip.name} - ID Cards`" />

    <!-- Print Controls (hidden when printing) -->
    <div class="print:hidden bg-slate-100 p-4 sticky top-0 z-10 border-b border-slate-200">
        <div class="max-w-[297mm] mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Umrah ID Cards</h1>
                <p class="text-sm text-slate-500">
                    {{ trip.name }} - {{ filteredCustomers.length }} of {{ customers.length }} selected
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a
                    :href="route('trips.show', trip.id)"
                    class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    Back to Trip
                </a>
                <button
                    type="button"
                    @click="showSelectionPanel = !showSelectionPanel"
                    class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Select Customers
                </button>
                <button
                    type="button"
                    @click="printCards"
                    :disabled="filteredCustomers.length === 0"
                    class="inline-flex items-center gap-2 rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print / Save as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Selection Panel (hidden when printing) -->
    <div v-if="showSelectionPanel" class="print:hidden bg-white border-b border-slate-200">
        <div class="max-w-[297mm] mx-auto p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-700">Select customers to generate ID cards</h2>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="selectAll"
                        class="text-xs font-medium text-blue-600 hover:text-blue-700"
                    >
                        Select All
                    </button>
                    <span class="text-slate-300">|</span>
                    <button
                        type="button"
                        @click="deselectAll"
                        class="text-xs font-medium text-slate-500 hover:text-slate-700"
                    >
                        Deselect All
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                <label
                    v-for="customer in customers"
                    :key="customer.id"
                    class="flex items-center gap-2 p-2 rounded-md border cursor-pointer transition-colors"
                    :class="selectedIds.has(customer.id) ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:bg-slate-50'"
                >
                    <input
                        type="checkbox"
                        :checked="selectedIds.has(customer.id)"
                        @change="toggleCustomer(customer.id)"
                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                    >
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-slate-900 truncate">{{ customer.umrah_id || '-' }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ customer.name_in_english || customer.name }}</div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Cards Pages -->
    <div v-if="filteredCustomers.length > 0" class="bg-slate-200 print:bg-white">
        <div
            v-for="(pageCustomers, pageIndex) in pages"
            :key="pageIndex"
            class="page bg-white mx-auto print:mx-0 print:shadow-none shadow-lg"
        >
            <!-- 2 columns x 2 rows grid -->
            <div class="card-grid">
                <div
                    v-for="(customer, cardIndex) in pageCustomers"
                    :key="customer.id"
                    class="card"
                >
                    <div class="card-inner">
                        <!-- LEFT SIDE - Back of card -->
                        <div class="card-side card-back">
                            <div class="back-content">
                                <!-- Hotel Info -->
                                <div v-if="primaryHotel" class="back-section">
                                    <div class="back-section-title">Hotel</div>
                                    <div v-if="primaryHotel.name_in_arabic" class="back-text-arabic">{{ primaryHotel.name_in_arabic }}</div>
                                    <div v-else class="back-text">{{ primaryHotel.name }}</div>
                                    <div v-if="primaryHotel.address" class="back-text-arabic">{{ primaryHotel.address }}</div>
                                </div>

                                <!-- Company Info -->
                                <div class="back-section">
                                    <div class="back-section-title">Maldives Office</div>
                                    <div v-if="companySettings?.address" class="back-text">{{ companySettings.address }}</div>
                                    <div v-if="companySettings?.phone" class="back-phone">{{ companySettings.phone }}</div>
                                </div>
                            </div>

                            <!-- QR Code - Bottom Half -->
                            <div class="back-qr">
                                <QrcodeVue
                                    :value="getQrUrl(customer)"
                                    :size="80"
                                    level="M"
                                    render-as="svg"
                                />
                            </div>
                        </div>

                        <!-- Center fold line -->
                        <div class="fold-line"></div>

                        <!-- RIGHT SIDE - Front of card -->
                        <div class="card-side card-front">
                            <!-- Top row: Logo left, Flag right -->
                            <div class="front-top-row">
                                <img src="/images/logo.png" alt="Logo" class="logo">
                                <img src="/images/flag-maldives.png" alt="Maldives" class="flag-image">
                            </div>

                            <!-- Title -->
                            <div class="card-title">UMRA ID CARD</div>

                            <!-- Photo -->
                            <div class="photo-frame">
                                <img
                                    v-if="customer.photo_path"
                                    :src="customer.photo_path"
                                    :alt="customer.name"
                                    class="photo"
                                >
                                <div v-else class="photo-placeholder">
                                    <svg class="photo-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Umrah ID -->
                            <div class="umrah-id">{{ customer.umrah_id || '-' }}</div>

                            <!-- Name -->
                            <div class="customer-name">{{ customer.name_in_english || customer.name }}</div>

                            <!-- Passport Number -->
                            <div class="passport-number">{{ customer.passport_number || '-' }}</div>

                            <!-- Phone Number (from trip) -->
                            <div class="phone-number">{{ trip.phone_number || '-' }}</div>

                            <!-- Bus Color Bar (only show if customer has a bus assigned) -->
                            <div
                                v-if="customer.bus_color"
                                class="bus-color-bar"
                                :style="{ backgroundColor: customer.bus_color }"
                            ></div>
                        </div>
                    </div>
                </div>
                <!-- Fill remaining slots with empty cards -->
                <div
                    v-for="n in (cardsPerPage - pageCustomers.length)"
                    :key="'empty-' + n"
                    class="card empty"
                >
                    <div class="card-inner">
                        <div class="card-side card-back"></div>
                        <div class="fold-line"></div>
                        <div class="card-side card-front"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty state -->
    <div v-if="customers.length === 0" class="print:hidden p-8 text-center">
        <p class="text-slate-500">No customers in this trip</p>
    </div>
    <div v-else-if="filteredCustomers.length === 0" class="print:hidden p-8 text-center">
        <p class="text-slate-500">No customers selected. Click "Select Customers" to choose which ID cards to generate.</p>
    </div>
</template>

<style scoped>
/* A4 landscape page dimensions */
.page {
    width: 297mm;
    height: 210mm;
    padding: 8mm 12mm;
    box-sizing: border-box;
    margin-bottom: 20px;
}

/* Grid layout: 2 columns x 2 rows */
.card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 6mm;
    height: 100%;
}

/* Individual card */
.card {
    border: 1px solid #d1d5db;
    border-radius: 4mm;
    overflow: hidden;
    background: white;
}

.card.empty {
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
}

.card-inner {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row;
}

/* Card sides */
.card-side {
    flex: 1;
    padding: 3mm;
    box-sizing: border-box;
}

.card-back {
    background: #fafafa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 4mm;
    padding-top: 6mm;
}

/* Back content wrapper - takes top half only */
.back-content {
    width: 100%;
    height: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    gap: 4mm;
    text-align: center;
}

/* Back of card sections */
.back-section {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    text-align: center;
}

.back-section-title {
    font-size: 7pt;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 1mm;
}

.back-text {
    font-size: 9pt;
    font-weight: 500;
    color: #334155;
    line-height: 1.3;
}

.back-text-arabic {
    font-size: 9pt;
    font-weight: 500;
    color: #334155;
    direction: rtl;
    line-height: 1.3;
}

.back-phone {
    font-size: 11pt;
    font-weight: 600;
    color: #0f172a;
    margin-top: 1mm;
}

/* QR Code section */
.back-qr {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: auto;
    padding-bottom: 2mm;
}

.card-front {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    padding-bottom: 0;
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Fold line */
.fold-line {
    width: 1px;
    background: #e2e8f0;
    border-left: 1px dashed #cbd5e1;
}

/* Top row with logo and flag */
.front-top-row {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2mm;
}

.logo {
    width: 12mm;
    height: 12mm;
    object-fit: contain;
}

.flag-image {
    width: 14mm;
    height: auto;
    border-radius: 1mm;
}

.card-title {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 10pt;
    font-weight: 600;
    font-style: normal;
    color: #334155;
    margin-bottom: 3mm;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.photo-frame {
    width: 28mm;
    height: 32mm;
    border: 2px solid #e5e7eb;
    border-radius: 5mm;
    overflow: hidden;
    background: #f9fafb;
    margin-bottom: 3mm;
}

.photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
}

.photo-icon {
    width: 14mm;
    height: 14mm;
}

/* Umrah ID */
.umrah-id {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 18pt;
    font-weight: 700;
    font-style: normal;
    color: #0f172a;
    letter-spacing: 0.5px;
    margin-bottom: 1.5mm;
}

/* Customer name */
.customer-name {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 11pt;
    font-weight: 600;
    font-style: normal;
    color: #1e293b;
    margin-bottom: 2mm;
}

/* Passport number */
.passport-number {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 10pt;
    font-weight: 500;
    font-style: normal;
    color: #475569;
    letter-spacing: 0.3px;
    margin-bottom: 2mm;
}

/* Phone number */
.phone-number {
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 13pt;
    font-weight: 600;
    font-style: normal;
    color: #0f172a;
    letter-spacing: 0.5px;
    margin-top: auto;
    margin-bottom: 7mm;
}

/* Bus color bar */
.bus-color-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 5mm;
    border-radius: 0 0 3mm 0;
}

/* Print styles */
@media print {
    @page {
        size: A4 landscape;
        margin: 0;
    }

    html, body {
        width: 297mm;
        height: 210mm;
        margin: 0;
        padding: 0;
    }

    .page {
        margin: 0;
        padding: 8mm 12mm;
        page-break-after: always;
        box-shadow: none;
    }

    .page:last-child {
        page-break-after: auto;
    }

    .card {
        border: 1px solid #9ca3af;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .card.empty {
        border: 1px dashed #9ca3af;
    }
}

/* Screen preview spacing */
@media screen {
    .page {
        margin: 20px auto;
    }
}
</style>
