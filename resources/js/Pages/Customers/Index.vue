<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { tkGetChar, toDhivehi } from '../../utils/lattinMapping';
import { searchIslands, getIslandOptions } from '../../utils/maldivesIslands';
import { searchCountries, getCountryOptions } from '../../utils/countries';

const props = defineProps({
    customers: Array,
});

const customerSearch = ref('');

const filteredCustomers = computed(() => {
    if (!customerSearch.value) {
        return props.customers;
    }
    const search = customerSearch.value.toLowerCase();
    return props.customers.filter(customer => {
        return (
            (customer.name && customer.name.toLowerCase().includes(search)) ||
            (customer.national_id && customer.national_id.toLowerCase().includes(search)) ||
            (customer.passport_number && customer.passport_number.toLowerCase().includes(search))
        );
    });
});

// Edit Modal
const showEditModal = ref(false);
const editingCustomer = ref(null);
const isForeigner = ref(false);

const editForm = useForm({
    name: '',
    national_id: '',
    passport_number: '',
    phone_number: '',
    island: '',
    address: '',
    gender: '',
    is_foreigner: false,
    country: '',
});

// Island dropdown state
const showIslandDropdown = ref(false);
const islandSearch = ref('');
const filteredIslands = computed(() => searchIslands(islandSearch.value));

// Country dropdown state
const showCountryDropdown = ref(false);
const countrySearch = ref('');
const filteredCountries = computed(() => searchCountries(countrySearch.value));

const selectedIslandLabel = computed(() => {
    if (!editForm.island) return '';
    const islands = getIslandOptions();
    const found = islands.find(i => i.value === editForm.island);
    return found ? found.label : editForm.island;
});

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

watch(isForeigner, (newValue) => {
    editForm.is_foreigner = newValue;
    if (newValue) {
        editForm.island = '';
    } else {
        editForm.country = '';
    }
});

const openEditModal = (customer) => {
    editingCustomer.value = customer;
    isForeigner.value = customer.is_foreigner || false;
    editForm.name = customer.name || '';
    editForm.national_id = customer.national_id || '';
    editForm.passport_number = customer.passport_number || '';
    editForm.phone_number = customer.phone_number || '';
    editForm.island = customer.island || '';
    editForm.address = customer.address || '';
    editForm.gender = customer.gender || '';
    editForm.is_foreigner = customer.is_foreigner || false;
    editForm.country = customer.country || '';
    editForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingCustomer.value = null;
    editForm.reset();
    editForm.clearErrors();
    islandSearch.value = '';
    countrySearch.value = '';
    showIslandDropdown.value = false;
    showCountryDropdown.value = false;
};

const submitEdit = () => {
    if (!editingCustomer.value) return;

    editForm.put(route('customers.update', editingCustomer.value.id), {
        onSuccess: () => {
            closeEditModal();
        },
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
</script>

<template>
    <Head title="Customers" />

    <main class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Customers</h1>
                <p class="mt-1 text-sm text-slate-500">All registered customers</p>
            </div>
        </div>

        <!-- Customers Section -->
        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <p class="text-sm text-slate-600">
                    {{ filteredCustomers.length }} customer{{ filteredCustomers.length !== 1 ? 's' : '' }}
                    <span v-if="customerSearch"> found</span>
                </p>
                <div class="relative">
                    <input
                        v-model="customerSearch"
                        type="text"
                        placeholder="Search by ID, passport, or name..."
                        class="w-72 rounded-lg border border-slate-200 px-3 py-2 pl-9 text-sm placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                    >
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table v-if="filteredCustomers.length > 0" class="min-w-full border-collapse text-sm" dir="rtl">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                        <tr>
                            <th class="border-b border-slate-200 px-4 py-3 text-center">#</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" lang="dv">ނަން</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" lang="dv">އައިޑީ ކާޑު</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" lang="dv">ޕާސްޕޯޓް</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" lang="dv">ފޯނު</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" lang="dv">ރަށް / ޤައުމު</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" lang="dv">އެޑްރެސް</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-right" lang="dv">ޖިންސު</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        <tr
                            v-for="(customer, index) in filteredCustomers"
                            :key="customer.id"
                            class="even:bg-slate-50/50 hover:bg-violet-50/50 transition-colors"
                        >
                            <td class="border-b border-slate-100 px-4 py-3 text-center text-slate-500">{{ index + 1 }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 font-medium text-right" lang="dv">{{ customer.name }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 font-mono text-right" dir="ltr">{{ customer.national_id || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 font-mono text-right" dir="ltr">{{ customer.passport_number || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 text-right" dir="ltr">{{ customer.phone_number || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 text-right" lang="dv">
                                <span v-if="customer.is_foreigner">{{ customer.country || '-' }}</span>
                                <span v-else>{{ customer.island || '-' }}</span>
                            </td>
                            <td class="border-b border-slate-100 px-4 py-3 text-right" lang="dv">{{ customer.address || '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 text-right" lang="dv">{{ customer.gender === 'Male' ? 'ފިރިހެން' : customer.gender === 'Female' ? 'އަންހެން' : '-' }}</td>
                            <td class="border-b border-slate-100 px-4 py-3 text-center">
                                <button
                                    type="button"
                                    @click="openEditModal(customer)"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 hover:text-violet-600"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5a2.121 2.121 0 00-3-3L5 17v3z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="p-12 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                        <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="font-medium text-slate-900">No customers found</p>
                    <p v-if="customerSearch" class="mt-1 text-sm text-slate-500">No results for "{{ customerSearch }}"</p>
                    <p v-else class="mt-1 text-sm text-slate-500">No customers registered yet</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Modal -->
    <Teleport to="body">
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
                        <div v-if="showEditModal" class="relative w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Edit Customer</h3>
                                    <p class="text-sm text-slate-500">Update customer information</p>
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

                            <!-- Local/Foreigner Toggle -->
                            <div class="mb-4 flex items-center gap-2 rounded-lg bg-slate-100 p-1 w-fit">
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

                            <form @submit.prevent="submitEdit" class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Name in Dhivehi</label>
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
                                    >
                                    <p v-if="editForm.errors.national_id" class="text-xs text-red-500 mt-1">{{ editForm.errors.national_id }}</p>
                                </div>

                                <!-- Passport Number (for foreigners) -->
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
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number</label>
                                    <input
                                        v-model="editForm.phone_number"
                                        type="number"
                                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-slate-900 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                                        required
                                    >
                                    <p v-if="editForm.errors.phone_number" class="text-xs text-red-500 mt-1">{{ editForm.errors.phone_number }}</p>
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
    </Teleport>
</template>
