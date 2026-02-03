<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);

const navItems = [
    { name: 'ހޯމް', href: '/' },
    { name: 'ގުޅުއްވާ', href: '#contact' },
    { name: 'ތަޢާރުފް', href: '/about' },
    { name: 'ޚިދުމަތްތައް', href: '#services' },
    { name: 'ދަތުރުތައް', href: '#trips' },
    { name: 'ގެލެރީ', href: '#gallery' },
    { name: 'ޑައުންލޯޑްސް', href: '#downloads' },
];

const isActive = (href) => {
    if (href === '/') {
        return currentUrl.value === '/';
    }
    if (href.startsWith('#')) {
        return false;
    }
    return currentUrl.value.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-slate-100">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="flex h-16 items-center gap-8" dir="rtl">
                    <!-- Logo -->
                    <Link href="/" class="flex-shrink-0">
                        <img src="/images/logo.png" alt="Zam Zam Logo" class="h-12 w-auto" />
                    </Link>

                    <!-- Company Name -->
                    <div class="hidden sm:block flex-shrink-0" lang="dv">
                        <p class="text-base font-bold text-slate-800">ޒަމްޒަމް ޙައްޖު އެންޑް ޢުމްރާ</p>
                        <p class="text-xs text-slate-400">ޕްރައިވެޓް ލިމިޓެޑް</p>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-6 mr-auto" lang="dv">
                        <template v-for="item in navItems" :key="item.name">
                            <Link
                                v-if="!item.href.startsWith('#')"
                                :href="item.href"
                                :class="[
                                    'text-sm font-medium transition-colors',
                                    isActive(item.href)
                                        ? 'text-teal-600'
                                        : 'text-slate-600 hover:text-slate-900',
                                ]"
                            >
                                {{ item.name }}
                            </Link>
                            <a
                                v-else
                                :href="item.href"
                                class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors"
                            >
                                {{ item.name }}
                            </a>
                        </template>
                    </nav>

                    <!-- Mobile menu button -->
                    <button class="md:hidden mr-auto p-2 text-slate-600 hover:text-slate-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-slate-800 text-white">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <div class="grid gap-8 md:grid-cols-3" dir="rtl" lang="dv">
                    <!-- Company Info -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <img src="/images/logo.png" alt="Zam Zam Logo" class="h-10 w-auto brightness-0 invert" />
                            <span class="font-bold text-lg">ޒަމްޒަމް</span>
                        </div>
                        <p class="text-slate-400 text-sm">
                            ދިވެހިރާއްޖެއިން ޢުމްރާ އަދި ޙައްޖު ދަތުރުގެ ޚިދުމަތް ފޯރުކޮށްދިނުމުގައި އިތުބާރު ހިފޭ ނަން.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="font-semibold mb-4">ލިންކްސް</h3>
                        <ul class="space-y-2 text-sm text-slate-400">
                            <li><Link href="/" class="hover:text-white transition-colors">ހޯމް</Link></li>
                            <li><Link href="/about" class="hover:text-white transition-colors">އަޅުގަނޑުމެން</Link></li>
                            <li><a href="#services" class="hover:text-white transition-colors">ޚިދުމަތްތައް</a></li>
                            <li><a href="#contact" class="hover:text-white transition-colors">ގުޅުއްވާ</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="font-semibold mb-4">ގުޅުއްވާ</h3>
                        <ul class="space-y-2 text-sm text-slate-400">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span style="font-family: ui-sans-serif, system-ui, sans-serif;">7999999</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span style="font-family: ui-sans-serif, system-ui, sans-serif;">info@zamzam.mv</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-700 text-center text-sm text-slate-400" dir="rtl" lang="dv">
                    <p>&copy; {{ new Date().getFullYear() }} ޒަމްޒަމް ޙައްޖު އެންޑް ޢުމްރާ. ހުރިހާ ޙައްޤެއް ލިބިގެންވޭ.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
