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
    if (!confirm('Are you sure you want to delete this group? Members will be unassigned.')) {
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

const getGroupName = (groupId) => {
    const group = groupTags.value.find(g => g.id === groupId);
    return group?.name || '';
};
</script>

<template>
    <Head title="Groups" />

    <main class="space-y-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Groups</h1>
                <p class="mt-1 text-slate-500">Manage customer groups</p>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                :disabled="groupForm.processing"
                @click="createGroup"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ groupForm.processing ? 'Creating...' : 'Add Group' }}
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="groupTags.length === 0" class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-900">No Groups</h3>
            <p class="mt-1 text-sm text-slate-500">Create a group to organize customers</p>
        </div>

        <!-- Group cards grid -->
        <div v-else class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="group in groupTags"
                :key="group.id"
                class="rounded-xl border border-slate-200 bg-white shadow-sm"
            >
                <!-- Card header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div v-if="editingGroup === group.id" class="flex flex-1 items-center gap-2">
                        <input
                            v-model="editForm.name"
                            type="text"
                            class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20"
                            @keyup.enter="saveEdit(group.id)"
                            @keyup.escape="cancelEdit"
                        >
                        <button
                            type="button"
                            class="rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-violet-700"
                            :disabled="editForm.processing"
                            @click="saveEdit(group.id)"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="rounded-lg px-2 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-100"
                            @click="cancelEdit"
                        >
                            Cancel
                        </button>
                    </div>
                    <template v-else>
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-violet-100 text-xs font-bold text-violet-700">
                                {{ group.tag }}
                            </span>
                            <h3 class="font-semibold text-slate-800" dir="rtl" lang="dv">{{ group.name }}</h3>
                        </div>
                        <div class="flex items-center">
                            <button
                                type="button"
                                class="rounded-md p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                title="Edit"
                                @click="startEdit(group)"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="rounded-md p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
                                title="Delete"
                                :disabled="deletingGroupId === group.id"
                                @click="deleteGroup(group.id)"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Card body - Members list -->
                <div class="p-3">
                    <div v-if="group.members.length === 0" class="py-8 text-center">
                        <p class="text-sm text-slate-400">No members</p>
                    </div>
                    <div v-else class="space-y-1.5">
                        <div
                            v-for="customer in group.members"
                            :key="customer.id"
                            class="group flex items-center justify-between rounded-lg px-3 py-2 transition hover:bg-slate-50"
                        >
                            <div class="flex flex-row-reverse items-center gap-3 min-w-0">
                                <span class="text-sm font-medium text-slate-700 truncate" dir="rtl" lang="dv">{{ customer.name }}</span>
                                <span class="inline-flex items-center justify-center rounded bg-violet-50 px-2 py-0.5 text-xs font-semibold text-violet-700 tabular-nums">
                                    {{ customer.pivot?.umrah_id || '-' }}
                                </span>
                            </div>
                            <button
                                type="button"
                                class="ml-2 rounded p-1 text-slate-300 opacity-0 transition hover:bg-red-50 hover:text-red-500 group-hover:opacity-100"
                                title="Remove"
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
                        class="mt-2 flex w-full items-center justify-center gap-1.5 rounded-lg border border-dashed border-slate-200 py-2 text-xs font-medium text-slate-400 transition hover:border-violet-300 hover:bg-violet-50 hover:text-violet-600"
                        @click="openAddMember(group.id)"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Member
                    </button>
                </div>
            </div>
        </div>

        <!-- Individuals section -->
        <div v-if="individuals.length > 0" class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-7 w-7 items-center justify-center rounded-md bg-slate-100">
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <h3 class="font-semibold text-slate-800">Individuals</h3>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                    {{ individuals.length }}
                </span>
            </div>
            <div class="p-4">
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="customer in individuals"
                        :key="customer.id"
                        class="inline-flex flex-row-reverse items-center gap-2 rounded-lg bg-slate-50 px-3 py-1.5"
                    >
                        <span class="text-sm font-medium text-slate-700" dir="rtl" lang="dv">{{ customer.name }}</span>
                        <span class="inline-flex items-center justify-center rounded bg-slate-200 px-1.5 py-0.5 text-xs font-semibold text-slate-600 tabular-nums">
                            {{ customer.pivot?.umrah_id || '-' }}
                        </span>
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
                        <div v-if="addMemberGroupId" class="relative w-full max-w-md rounded-xl bg-white shadow-xl">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">Add Members</h3>
                                    <p class="text-sm text-slate-500">
                                        Adding to <span class="font-medium text-violet-600">{{ getGroupTag(addMemberGroupId) }}</span>
                                        <span v-if="getGroupName(addMemberGroupId)" dir="rtl" lang="dv"> - {{ getGroupName(addMemberGroupId) }}</span>
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="closeAddMember"
                                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Modal body -->
                            <div class="max-h-80 overflow-y-auto p-2">
                                <div v-if="availableCustomers.length === 0" class="py-12 text-center">
                                    <p class="text-sm text-slate-400">No available customers</p>
                                </div>
                                <div v-else class="space-y-1">
                                    <div
                                        v-for="customer in availableCustomers"
                                        :key="customer.id"
                                        class="flex items-center justify-between rounded-lg px-3 py-2.5 transition hover:bg-slate-50"
                                    >
                                        <div class="flex flex-row-reverse items-center gap-3 min-w-0">
                                            <div class="min-w-0 text-right">
                                                <p class="text-sm font-medium text-slate-800 truncate" dir="rtl" lang="dv">{{ customer.name }}</p>
                                                <p class="text-xs text-slate-400">{{ customer.national_id }}</p>
                                            </div>
                                            <span class="inline-flex items-center justify-center rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600 tabular-nums">
                                                {{ customer.pivot?.umrah_id || '-' }}
                                            </span>
                                        </div>
                                        <button
                                            type="button"
                                            class="ml-2 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-violet-700 disabled:opacity-50"
                                            :disabled="assigningCustomerId === customer.id"
                                            @click="addToGroup(customer.id, addMemberGroupId)"
                                        >
                                            {{ assigningCustomerId === customer.id ? '...' : 'Add' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="border-t border-slate-100 px-5 py-3">
                                <button
                                    type="button"
                                    class="w-full rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-200"
                                    @click="closeAddMember"
                                >
                                    Done
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
