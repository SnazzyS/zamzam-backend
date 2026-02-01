<script>
export default {
    layout: null,
};
</script>

<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    trip: Object,
    flight: Object,
    passengers: Array,
});

// Available columns configuration
const availableColumns = [
    { key: 'umrah_id', label: 'ހާޖީ ނަންބަރު', labelEn: 'Hajee Number', default: true, locked: true },
    { key: 'name', label: 'ނަން', labelEn: 'Name (Dhivehi)', default: true },
    { key: 'name_in_english', label: 'ނަން (އިނގިރޭސި)', labelEn: 'Name (English)', default: false },
    { key: 'island', label: 'ރަށް', labelEn: 'Island', default: true },
    { key: 'national_id', label: 'އައިޑީ ކާޑު ނަންބަރު', labelEn: 'ID Card', default: true },
    { key: 'passport_number', label: 'ޕާސްޕޯޓް ނަންބަރު', labelEn: 'Passport', default: false },
    { key: 'phone_number', label: 'ފޯނު ނަންބަރު', labelEn: 'Phone', default: true },
    { key: 'date_of_birth', label: 'އުފަންދުވަސް', labelEn: 'Date of Birth', default: false },
    { key: 'gender', label: 'ޖިންސް', labelEn: 'Gender', default: false },
    { key: 'departure_date', label: 'ފުރާ ތާރީޚް', labelEn: 'Departure Date', default: false, fromFlight: true },
];

// Selected columns (keys)
const selectedColumnKeys = ref(
    availableColumns.filter(c => c.default).map(c => c.key)
);

// Ordered columns for display
const orderedColumns = ref([...selectedColumnKeys.value]);

// State to show list
const showList = ref(false);

// Toggle column selection
const toggleColumn = (key) => {
    const column = availableColumns.find(c => c.key === key);
    if (column?.locked) return;

    const index = selectedColumnKeys.value.indexOf(key);
    if (index > -1) {
        selectedColumnKeys.value.splice(index, 1);
        orderedColumns.value = orderedColumns.value.filter(k => k !== key);
    } else {
        selectedColumnKeys.value.push(key);
        orderedColumns.value.push(key);
    }
};

// Move column up in order
const moveUp = (key) => {
    const index = orderedColumns.value.indexOf(key);
    // Can't move up if first item or if it's umrah_id (always first after #)
    if (index <= 0 || key === 'umrah_id') return;
    // Can't move above umrah_id
    if (orderedColumns.value[index - 1] === 'umrah_id') return;

    const temp = orderedColumns.value[index - 1];
    orderedColumns.value[index - 1] = orderedColumns.value[index];
    orderedColumns.value[index] = temp;
};

// Move column down in order
const moveDown = (key) => {
    const index = orderedColumns.value.indexOf(key);
    // Can't move down if last item or if it's umrah_id (always first after #)
    if (index >= orderedColumns.value.length - 1 || key === 'umrah_id') return;

    const temp = orderedColumns.value[index + 1];
    orderedColumns.value[index + 1] = orderedColumns.value[index];
    orderedColumns.value[index] = temp;
};

// Get column config by key
const getColumn = (key) => availableColumns.find(c => c.key === key);

// Computed columns for display (always starts with umrah_id)
const displayColumns = computed(() => {
    // Ensure umrah_id is always first
    const cols = orderedColumns.value.filter(k => selectedColumnKeys.value.includes(k));
    const umrahIndex = cols.indexOf('umrah_id');
    if (umrahIndex > 0) {
        cols.splice(umrahIndex, 1);
        cols.unshift('umrah_id');
    }
    return cols;
});

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

// Format gender
const formatGender = (gender) => {
    if (!gender) return '-';
    if (gender === 'male') return 'ފިރިހެން';
    if (gender === 'female') return 'އަންހެން';
    return gender;
};

// Get cell value
const getCellValue = (passenger, key) => {
    // Handle flight-level columns
    const column = getColumn(key);
    if (column?.fromFlight) {
        const value = props.flight[key];
        if (value === null || value === undefined || value === '') return '-';
        if (key === 'departure_date') return formatDate(value);
        return value;
    }

    const value = passenger[key];
    if (value === null || value === undefined || value === '') return '-';
    if (key === 'date_of_birth') return formatDate(value);
    if (key === 'gender') return formatGender(value);
    return value;
};

// Generate list
const generateList = () => {
    showList.value = true;
};

// Back to configuration
const backToConfig = () => {
    showList.value = false;
};

// Print
const printList = () => {
    window.print();
};
</script>

<template>
    <Head :title="`${flight.name} - Passenger List`" />

    <!-- Configuration Panel (hidden when printing) -->
    <div v-if="!showList" class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">Configure Passenger List</h1>
                        <p class="text-sm text-slate-500 mt-1">{{ flight.name }} - {{ trip.name }}</p>
                    </div>
                    <a
                        :href="route('trips.flights.show', [trip.id, flight.id])"
                        class="text-slate-400 hover:text-slate-600 transition"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>

                <!-- Column Selection -->
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-slate-700 mb-3">Select Columns</h2>
                    <p class="text-xs text-slate-500 mb-3"># column is always included. Hajee Number is required.</p>
                    <div class="space-y-2">
                        <label
                            v-for="col in availableColumns"
                            :key="col.key"
                            class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer"
                            :class="{ 'opacity-60': col.locked }"
                        >
                            <input
                                type="checkbox"
                                :checked="selectedColumnKeys.includes(col.key)"
                                :disabled="col.locked"
                                @change="toggleColumn(col.key)"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-sm text-slate-700">{{ col.labelEn }}</span>
                            <span class="text-xs text-slate-400" dir="rtl">({{ col.label }})</span>
                            <span v-if="col.locked" class="text-xs text-amber-600 ml-auto">Required</span>
                        </label>
                    </div>
                </div>

                <!-- Column Order -->
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-slate-700 mb-3">Column Order</h2>
                    <p class="text-xs text-slate-500 mb-3">Drag or use arrows to reorder. # is always first, Hajee Number is always second.</p>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 p-2 bg-slate-100 rounded text-sm text-slate-500">
                            <span class="w-8 text-center">#</span>
                            <span>Row Number (Always First)</span>
                        </div>
                        <div
                            v-for="(key, index) in orderedColumns.filter(k => selectedColumnKeys.includes(k))"
                            :key="key"
                            class="flex items-center gap-2 p-2 bg-white border border-slate-200 rounded"
                        >
                            <span class="w-8 text-center text-xs text-slate-400">{{ index + 2 }}</span>
                            <span class="flex-1 text-sm text-slate-700">{{ getColumn(key)?.labelEn }}</span>
                            <div class="flex gap-1">
                                <button
                                    type="button"
                                    @click="moveUp(key)"
                                    :disabled="key === 'umrah_id' || index === 0 || orderedColumns[orderedColumns.indexOf(key) - 1] === 'umrah_id'"
                                    class="p-1 rounded hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click="moveDown(key)"
                                    :disabled="key === 'umrah_id' || index === orderedColumns.filter(k => selectedColumnKeys.includes(k)).length - 1"
                                    class="p-1 rounded hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    type="button"
                    @click="generateList"
                    class="w-full py-3 px-4 bg-blue-500 text-white font-medium rounded-md hover:bg-blue-600 transition"
                >
                    Generate Passenger List
                </button>
            </div>
        </div>
    </div>

    <!-- Printable List -->
    <div v-else class="min-h-screen bg-white p-8 print:p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Print Controls (hidden when printing) -->
            <div class="flex items-center justify-between mb-6 print:hidden">
                <button
                    type="button"
                    @click="backToConfig"
                    class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Configuration
                </button>
                <button
                    type="button"
                    @click="printList"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </button>
            </div>

            <!-- Header with Logo -->
            <div class="text-center mb-6">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <img src="/images/logo.png" alt="Zam Zam" class="h-12 w-auto" onerror="this.style.display='none'">
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-800 leading-tight">Zam Zam</p>
                        <p class="text-xs text-slate-600 leading-tight">Hajj & Umra Travel</p>
                    </div>
                </div>

                <!-- Trip Info -->
                <h1 class="text-2xl font-bold text-slate-800 mb-1" dir="rtl" lang="dv">
                    {{ trip.name }}
                </h1>

                <!-- Flight Info -->
                <p class="text-lg text-slate-600" dir="rtl" lang="dv">
                    {{ flight.name }}
                </p>
            </div>

            <!-- Passengers Table (RTL - # on right) -->
            <table class="w-full border-collapse text-sm" dir="rtl" lang="dv">
                <thead>
                    <tr class="border-b-2 border-slate-300">
                        <th class="py-2 px-3 text-center font-semibold text-slate-700 w-12">#</th>
                        <th
                            v-for="key in displayColumns"
                            :key="key"
                            class="py-2 px-3 text-right font-semibold text-slate-700"
                        >
                            {{ getColumn(key)?.label }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(passenger, index) in passengers"
                        :key="passenger.id"
                        class="border-b border-slate-200"
                    >
                        <td class="py-2 px-3 text-center">{{ index + 1 }}</td>
                        <td
                            v-for="key in displayColumns"
                            :key="key"
                            class="py-2 px-3 text-right"
                            :class="{
                                'font-mono': ['national_id', 'passport_number', 'umrah_id'].includes(key),
                            }"
                            :dir="['national_id', 'passport_number', 'phone_number'].includes(key) ? 'ltr' : 'rtl'"
                        >
                            {{ getCellValue(passenger, key) }}
                        </td>
                    </tr>
                    <tr v-if="passengers.length === 0">
                        <td :colspan="displayColumns.length + 1" class="py-8 text-center text-slate-400">
                            No passengers assigned to this flight
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Footer with count -->
            <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    Total: <span class="font-semibold">{{ passengers.length }}</span> passengers
                </p>
                <p class="text-xs text-slate-400 print:hidden">
                    <a
                        :href="route('trips.flights.show', [trip.id, flight.id])"
                        class="text-blue-600 hover:text-blue-800"
                    >
                        Back to Flight
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>
