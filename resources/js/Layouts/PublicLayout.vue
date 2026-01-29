<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);

const navItems = [
    { name: 'ހޯމް', href: '/' },
    { name: 'އަޅުގަނޑުމެން', href: '/about' },
];

const isActive = (href) => {
    if (href === '/') {
        return currentUrl.value === '/';
    }
    return currentUrl.value.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-600">
                        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-slate-900">Zam Zam</span>
                </Link>

                <!-- Navigation Links -->
                <div class="flex items-center gap-1" dir="rtl" lang="dv">
                    <Link
                        v-for="item in navItems"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'rounded-lg px-4 py-2 text-sm font-medium transition',
                            isActive(item.href)
                                ? 'bg-violet-100 text-violet-700'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                        ]"
                    >
                        {{ item.name }}
                    </Link>
                </div>

                <!-- CTA Button -->
                <Link
                    href="/office/dashboard"
                    class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-700"
                    dir="rtl"
                    lang="dv"
                >
                    އޮފީސް ޕޯޓަލް
                </Link>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-slate-50">
            <div class="mx-auto max-w-6xl px-6 py-12">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-600">
                            <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-700" dir="rtl" lang="dv">ޒަމްޒަމް</span>
                    </div>
                    <p class="text-sm text-slate-500" dir="rtl" lang="dv">
                        &copy; {{ new Date().getFullYear() }} ޒަމްޒަމް. ހުރިހާ ޙައްޤެއް ލިބިގެންވޭ.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
