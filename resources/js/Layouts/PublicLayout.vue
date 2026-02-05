<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const currentUrl = computed(() => page.url);
const mobileMenuOpen = ref(false);
const scrolled = ref(false);

const navItems = [
    { name: 'Home', href: '/' },
    { name: 'Trips', href: '#trips' },
    { name: 'Services', href: '#services' },
    { name: 'Gallery', href: '#gallery' },
    { name: 'Contact', href: '#contact' },
];

const isActive = (href) => {
    if (href === '/') return currentUrl.value === '/';
    return currentUrl.value.startsWith(href);
};

const handleScroll = () => {
    scrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <!-- Navbar -->
        <header
            :class="[
                'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
                scrolled ? 'bg-white shadow-lg' : 'bg-transparent'
            ]"
        >
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex h-20 items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-3">
                        <img src="/images/logo.png" alt="Zamzam" class="h-12 w-auto" />
                        <span :class="['font-bold text-xl transition-colors', scrolled ? 'text-slate-900' : 'text-white']">
                            Zamzam
                        </span>
                    </Link>

                    <!-- Desktop Nav -->
                    <nav class="hidden md:flex items-center gap-8">
                        <template v-for="item in navItems" :key="item.name">
                            <Link
                                v-if="!item.href.startsWith('#')"
                                :href="item.href"
                                :class="[
                                    'text-sm font-medium transition-colors',
                                    scrolled
                                        ? (isActive(item.href) ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600')
                                        : (isActive(item.href) ? 'text-white' : 'text-white/80 hover:text-white'),
                                ]"
                            >
                                {{ item.name }}
                            </Link>
                            <a
                                v-else
                                :href="item.href"
                                :class="[
                                    'text-sm font-medium transition-colors',
                                    scrolled
                                        ? 'text-slate-600 hover:text-emerald-600'
                                        : 'text-white/80 hover:text-white',
                                ]"
                            >
                                {{ item.name }}
                            </a>
                        </template>
                        <a
                            href="https://wa.me/9607999999"
                            target="_blank"
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-sm font-medium px-6 py-2.5 rounded-full transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40"
                        >
                            Book Now
                        </a>
                    </nav>

                    <!-- Mobile menu button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        :class="[
                            'md:hidden p-2 rounded-lg transition',
                            scrolled ? 'text-slate-600 hover:bg-slate-100' : 'text-white hover:bg-white/10'
                        ]"
                    >
                        <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-white border-t shadow-xl">
                <nav class="px-6 py-4 space-y-2">
                    <template v-for="item in navItems" :key="item.name">
                        <Link
                            v-if="!item.href.startsWith('#')"
                            :href="item.href"
                            class="block py-3 text-slate-600 hover:text-emerald-600 font-medium"
                            @click="mobileMenuOpen = false"
                        >
                            {{ item.name }}
                        </Link>
                        <a
                            v-else
                            :href="item.href"
                            class="block py-3 text-slate-600 hover:text-emerald-600 font-medium"
                            @click="mobileMenuOpen = false"
                        >
                            {{ item.name }}
                        </a>
                    </template>
                    <a
                        href="https://wa.me/9607999999"
                        target="_blank"
                        class="block w-full text-center bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium py-3 rounded-full mt-4"
                    >
                        Book Now
                    </a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-white">
            <div class="max-w-7xl mx-auto px-6 py-16">
                <div class="grid md:grid-cols-4 gap-12">
                    <!-- Brand -->
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3 mb-6">
                            <img src="/images/logo.png" alt="Zamzam" class="h-12 w-auto brightness-0 invert" />
                            <span class="font-bold text-2xl">Zamzam</span>
                        </div>
                        <p class="text-slate-400 leading-relaxed max-w-md">
                            Your trusted partner for Hajj and Umrah journeys from the Maldives. Making spiritual dreams come true since 2009.
                        </p>
                        <div class="flex gap-4 mt-6">
                            <a href="#" class="w-10 h-10 bg-slate-800 hover:bg-emerald-500 rounded-full flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-slate-800 hover:bg-emerald-500 rounded-full flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="https://wa.me/9607999999" target="_blank" class="w-10 h-10 bg-slate-800 hover:bg-green-500 rounded-full flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="font-semibold text-lg mb-6">Quick Links</h4>
                        <ul class="space-y-3">
                            <li><a href="#trips" class="text-slate-400 hover:text-emerald-400 transition">Upcoming Trips</a></li>
                            <li><a href="#services" class="text-slate-400 hover:text-emerald-400 transition">Our Services</a></li>
                            <li><a href="#gallery" class="text-slate-400 hover:text-emerald-400 transition">Gallery</a></li>
                            <li><a href="#contact" class="text-slate-400 hover:text-emerald-400 transition">Contact Us</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h4 class="font-semibold text-lg mb-6">Contact</h4>
                        <ul class="space-y-3 text-slate-400">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                +960 7999999
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                info@zamzam.mv
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Male', Maldives
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-800 text-center text-slate-500 text-sm">
                    &copy; {{ new Date().getFullYear() }} Zamzam Hajj & Umrah Pvt Ltd. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
