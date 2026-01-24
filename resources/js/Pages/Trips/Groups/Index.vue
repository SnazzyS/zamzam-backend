<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    trip: Object,
    groups: Array,
    customers: Array,
});

const groupForm = useForm({
    name: '',
});

const createGroup = () => {
    groupForm.post(route('trips.groups.store', props.trip.id), {
        preserveScroll: true,
        onSuccess: () => {
            groupForm.reset('name');
        },
    });
};

const groupTags = computed(() => {
    return (props.groups || []).map((group, index) => ({
        ...group,
        tag: `G${index + 1}`,
        members: (props.customers || []).filter(c => c.pivot?.group_id === group.id),
    }));
});

const individuals = computed(() => {
    return (props.customers || []).filter(c => !c.pivot?.group_id);
});

// Edit group
const editingGroup = ref(null);
const editForm = useForm({
    name: '',
});

const startEdit = (group) => {
    editingGroup.value = group.id;
    editForm.name = group.name;
};

const cancelEdit = () => {
    editingGroup.value = null;
    editForm.reset();
};

const saveEdit = (groupId) => {
    editForm.put(route('trips.groups.update', [props.trip.id, groupId]), {
        preserveScroll: true,
        onSuccess: () => {
            editingGroup.value = null;
            editForm.reset();
        },
    });
};

// Delete group
const deletingGroupId = ref(null);

const deleteGroup = (groupId) => {
    if (!confirm('މި ގްރޫޕް ޑިލީޓް ކުރަން ބޭނުންތަ؟ މެންބަރުން ގްރޫޕުން ވަކިވާނެ.')) {
        return;
    }
    deletingGroupId.value = groupId;
    router.delete(route('trips.groups.destroy', [props.trip.id, groupId]), {
        preserveScroll: true,
        onFinish: () => {
            deletingGroupId.value = null;
        },
    });
};

// Add member modal
const addMemberGroupId = ref(null);

const openAddMember = (groupId) => {
    addMemberGroupId.value = groupId;
};

const closeAddMember = () => {
    addMemberGroupId.value = null;
};

const availableCustomers = computed(() => {
    if (!addMemberGroupId.value) return [];
    // Only show individuals (customers not in any group)
    return individuals.value;
});

const assigningCustomerId = ref(null);

const addToGroup = (customerId, groupId) => {
    assigningCustomerId.value = customerId;
    router.put(
        route('trips.customers.assign-group', [props.trip.id, customerId]),
        { group_id: groupId },
        {
            preserveScroll: true,
            onFinish: () => {
                assigningCustomerId.value = null;
            },
        }
    );
};

const removeFromGroup = (customerId) => {
    assigningCustomerId.value = customerId;
    router.put(
        route('trips.customers.assign-group', [props.trip.id, customerId]),
        { group_id: null },
        {
            preserveScroll: true,
            onFinish: () => {
                assigningCustomerId.value = null;
            },
        }
    );
};

const getGroupTag = (groupId) => {
    const group = groupTags.value.find(g => g.id === groupId);
    return group?.tag || '';
};
</script>

<template>
    <Head title="ގްރޫޕްތައް" />

    <main class="space-y-6" dir="rtl" lang="dv">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">ގްރޫޕްތައް</h1>
                <p class="mt-1 text-slate-500">ގްރޫޕުތައް މެނޭޖް ކުރޭ</p>
            </div>

            <button
                type="button"
                class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                :disabled="groupForm.processing"
                @click="createGroup"
            >
                <span v-if="groupForm.processing">ތައްޔާރު ކުރަނީ...</span>
                <span v-else class="flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    ގްރޫޕް އިތުރުކުރޭ
                </span>
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="groupTags.length === 0" class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-slate-900">ގްރޫޕެއް ނެތް</h3>
            <p class="mt-1 text-sm text-slate-500">ކަސްޓަމަރުން ބެހެއްޓުމަށް ގްރޫޕެއް ހައްދަވާ</p>
        </div>

        <!-- Group cards grid -->
        <div v-else class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="group in groupTags"
                :key="group.id"
                class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden"
            >
                <!-- Card header -->
                <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                    <div v-if="editingGroup === group.id" class="flex items-center gap-2">
                        <input
                            v-model="editForm.name"
                            type="text"
                            class="flex-1 rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                            @keyup.enter="saveEdit(group.id)"
                            @keyup.escape="cancelEdit"
                        >
                        <button
                            type="button"
                            class="rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-violet-700 disabled:opacity-50"
                            :disabled="editForm.processing"
                            @click="saveEdit(group.id)"
                        >
                            ރައްކާކުރޭ
                        </button>
                        <button
                            type="button"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-200"
                            @click="cancelEdit"
                        >
                            ކެންސަލް
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 text-xs font-bold text-violet-700">
                                {{ group.tag }}
                            </span>
                            <h3 class="font-semibold text-slate-900">{{ group.name }}</h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-200 hover:text-slate-600"
                                title="Edit group"
                                @click="startEdit(group)"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-50"
                                title="Delete group"
                                :disabled="deletingGroupId === group.id"
                                @click="deleteGroup(group.id)"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card body - Members list -->
                <div class="p-4">
                    <div v-if="group.members.length === 0" class="py-6 text-center text-sm text-slate-400">
                        މި ގްރޫޕުގައި މެންބަރެއް ނެތް
                    </div>
                    <div v-else class="space-y-2">
                        <div
                            v-for="customer in group.members"
                            :key="customer.id"
                            class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2"
                        >
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-violet-100 text-violet-600 text-xs font-medium">
                                    {{ customer.name?.charAt(0) || '?' }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900" dir="rtl" lang="dv">{{ customer.name }}</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="text-slate-400 transition hover:text-red-600 disabled:opacity-50"
                                title="Remove from group"
                                :disabled="assigningCustomerId === customer.id"
                                @click="removeFromGroup(customer.id)"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Add member button -->
                    <button
                        type="button"
                        class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-slate-300 px-3 py-2 text-sm text-slate-500 transition hover:border-violet-400 hover:bg-violet-50 hover:text-violet-600"
                        @click="openAddMember(group.id)"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        މެންބަރު އިތުރުކުރޭ
                    </button>
                </div>
            </div>
        </div>

        <!-- Individuals section -->
        <div v-if="individuals.length > 0" class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-xs font-bold text-slate-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <h3 class="font-semibold text-slate-900">ވަކިވަކި ފަރުދުން</h3>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
                        {{ individuals.length }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                        v-for="customer in individuals"
                        :key="customer.id"
                        class="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2"
                    >
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-slate-600 text-xs font-medium">
                            {{ customer.name?.charAt(0) || '?' }}
                        </div>
                        <p class="text-sm font-medium text-slate-900 truncate" dir="rtl" lang="dv">{{ customer.name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Member Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="addMemberGroupId" class="fixed inset-0 z-[100] overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeAddMember"></div>

                    <Transition
                        enter-active-class="duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="addMemberGroupId" class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-xl" dir="rtl" lang="dv">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ getGroupTag(addMemberGroupId) }} އަށް މެންބަރުން އިތުރުކުރޭ</h3>
                                    <p class="text-sm text-slate-500">މި ގްރޫޕަށް އިތުރުކުރާނެ ކަސްޓަމަރުން ޚިޔާރުކުރޭ</p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeAddMember"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                <div v-if="availableCustomers.length === 0" class="py-8 text-center text-sm text-slate-400">
                                    އިތުރުކުރެވެން ހުރި ކަސްޓަމަރެއް ނެތް
                                </div>
                                <div v-else class="space-y-2">
                                    <div
                                        v-for="customer in availableCustomers"
                                        :key="customer.id"
                                        class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2 hover:bg-slate-50"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                                                {{ customer.name?.charAt(0) || '?' }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900" dir="rtl" lang="dv">{{ customer.name }}</p>
                                                <p class="text-xs text-slate-500">{{ customer.national_id }}</p>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            class="rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                                            :disabled="assigningCustomerId === customer.id"
                                            @click="addToGroup(customer.id, addMemberGroupId)"
                                        >
                                            {{ assigningCustomerId === customer.id ? '...' : 'އިތުރުކުރޭ' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 flex justify-start border-t border-slate-100 pt-4">
                                <button
                                    type="button"
                                    class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                                    @click="closeAddMember"
                                >
                                    ނިމުނީ
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
