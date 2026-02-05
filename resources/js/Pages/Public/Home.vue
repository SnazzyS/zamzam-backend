<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

const trips = [
    {
        id: 1,
        title: 'Ramadan Umrah Package',
        image: 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
        date: 'March 12, 2026',
        duration: '14 Days',
        price: '$2,499',
        spots: 12,
        status: 'open',
    },
    {
        id: 2,
        title: 'Shawwal Umrah Special',
        image: 'https://images.unsplash.com/photo-1565019001607-0abe79ace455?w=800&q=80',
        date: 'April 3, 2026',
        duration: '10 Days',
        price: '$1,899',
        spots: 8,
        status: 'open',
    },
    {
        id: 3,
        title: 'Hajj 2026 Premium',
        image: 'https://images.unsplash.com/photo-1580418827493-f2b22c0a76cb?w=800&q=80',
        date: 'June 1, 2026',
        duration: '21 Days',
        price: '$5,999',
        spots: 0,
        status: 'waitlist',
    },
];

const services = [
    {
        icon: '🕋',
        title: 'Umrah Packages',
        desc: 'Year-round Umrah trips with flexible dates and premium accommodations.',
        color: 'from-violet-500 to-purple-500',
    },
    {
        icon: '🌙',
        title: 'Hajj Packages',
        desc: 'Complete Hajj packages with experienced guides and 5-star hotels.',
        color: 'from-amber-500 to-orange-500',
    },
    {
        icon: '🏨',
        title: 'Hotel Booking',
        desc: 'Premium hotels near Haram in both Makkah and Madinah.',
        color: 'from-emerald-500 to-teal-500',
    },
    {
        icon: '✈️',
        title: 'Flight & Visa',
        desc: 'Complete travel arrangements including flights and visa processing.',
        color: 'from-blue-500 to-cyan-500',
    },
    {
        icon: '🚐',
        title: 'Transportation',
        desc: 'Airport transfers and Ziyarat tours in air-conditioned vehicles.',
        color: 'from-pink-500 to-rose-500',
    },
    {
        icon: '📞',
        title: '24/7 Support',
        desc: 'Round-the-clock assistance throughout your spiritual journey.',
        color: 'from-indigo-500 to-blue-500',
    },
];

const gallery = [
    'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=600&q=80',
    'https://images.unsplash.com/photo-1565019001607-0abe79ace455?w=600&q=80',
    'https://images.unsplash.com/photo-1580418827493-f2b22c0a76cb?w=600&q=80',
    'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=600&q=80',
    'https://images.unsplash.com/photo-1519817650390-64a93db51149?w=600&q=80',
    'https://images.unsplash.com/photo-1466442929976-97f336a657be?w=600&q=80',
];

const stats = [
    { value: '15+', label: 'Years Experience', icon: '🏆' },
    { value: '5,000+', label: 'Happy Pilgrims', icon: '😊' },
    { value: '50+', label: 'Successful Trips', icon: '✈️' },
    { value: '98%', label: 'Satisfaction Rate', icon: '⭐' },
];

const testimonials = [
    {
        name: 'Ahmed Hassan',
        text: 'An unforgettable spiritual journey. The team took care of everything perfectly!',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80',
    },
    {
        name: 'Fatima Ali',
        text: 'Best Umrah experience ever. Highly recommend Zamzam to everyone.',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80',
    },
    {
        name: 'Mohamed Ibrahim',
        text: 'Professional service from start to finish. Will definitely travel with them again.',
        avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&q=80',
    },
];
</script>

<template>
    <Head title="Zamzam - Hajj & Umrah Services" />

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=1920&q=80"
                alt="Kaaba"
                class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-transparent"></div>
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-6 py-32">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 text-sm text-white/90 mb-8">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Bookings Open for Ramadan 2026
                </div>

                <h1 class="text-5xl md:text-7xl font-bold text-white leading-tight">
                    Your Journey to the
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-400">
                        Holy Land
                    </span>
                    Starts Here
                </h1>

                <p class="mt-6 text-xl text-white/80 leading-relaxed">
                    Experience the spiritual journey of a lifetime with Zamzam. We've been helping pilgrims fulfill their dreams for over 15 years.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a
                        href="#trips"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold px-8 py-4 rounded-full transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50"
                    >
                        View Packages
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a
                        href="https://wa.me/9607999999"
                        target="_blank"
                        class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-semibold px-8 py-4 rounded-full transition-all border border-white/20"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Chat on WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div v-for="stat in stats" :key="stat.label" class="text-center">
                    <div class="text-4xl mb-2">{{ stat.icon }}</div>
                    <div class="text-4xl font-bold text-slate-900">{{ stat.value }}</div>
                    <div class="text-slate-500 mt-1">{{ stat.label }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trips Section -->
    <section id="trips" class="py-24 bg-gradient-to-b from-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium mb-4">
                    Upcoming Trips
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900">
                    Choose Your
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">
                        Journey
                    </span>
                </h2>
                <p class="mt-4 text-xl text-slate-600 max-w-2xl mx-auto">
                    Book your spot on our upcoming Hajj and Umrah packages
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div
                    v-for="trip in trips"
                    :key="trip.id"
                    class="group bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-slate-300/50 transition-all duration-300 hover:-translate-y-2"
                >
                    <!-- Image -->
                    <div class="relative h-56 overflow-hidden">
                        <img
                            :src="trip.image"
                            :alt="trip.title"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                        />
                        <div class="absolute top-4 right-4">
                            <span
                                :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium',
                                    trip.status === 'open'
                                        ? 'bg-emerald-500 text-white'
                                        : 'bg-amber-500 text-white'
                                ]"
                            >
                                {{ trip.status === 'open' ? `${trip.spots} spots left` : 'Waitlist' }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ trip.title }}</h3>
                        <div class="flex items-center gap-4 text-slate-500 text-sm mb-4">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ trip.date }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ trip.duration }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 text-sm">Starting from</span>
                                <p class="text-2xl font-bold text-emerald-600">{{ trip.price }}</p>
                            </div>
                            <a
                                href="https://wa.me/9607999999"
                                target="_blank"
                                class="bg-slate-900 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-full text-sm font-medium transition"
                            >
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 bg-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-white/10 text-emerald-400 rounded-full text-sm font-medium mb-4">
                    Our Services
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-white">
                    Everything You Need
                </h2>
                <p class="mt-4 text-xl text-slate-400 max-w-2xl mx-auto">
                    From visa processing to hotel bookings, we handle it all
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="service in services"
                    :key="service.title"
                    class="group relative bg-white/5 backdrop-blur-sm rounded-2xl p-8 hover:bg-white/10 transition-all border border-white/10 hover:border-white/20"
                >
                    <div
                        :class="[
                            'w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-6 bg-gradient-to-br',
                            service.color
                        ]"
                    >
                        {{ service.icon }}
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">{{ service.title }}</h3>
                    <p class="text-slate-400 leading-relaxed">{{ service.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium mb-4">
                    Gallery
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900">
                    Capture the
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">
                        Moments
                    </span>
                </h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div
                    v-for="(image, index) in gallery"
                    :key="index"
                    :class="[
                        'relative overflow-hidden rounded-2xl group',
                        index === 0 ? 'md:col-span-2 md:row-span-2' : ''
                    ]"
                >
                    <img
                        :src="image"
                        alt="Gallery"
                        :class="[
                            'w-full object-cover group-hover:scale-110 transition-transform duration-500',
                            index === 0 ? 'h-full min-h-[400px]' : 'h-64'
                        ]"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-gradient-to-b from-emerald-50 to-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium mb-4">
                    Testimonials
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900">
                    What Our Pilgrims Say
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div
                    v-for="testimonial in testimonials"
                    :key="testimonial.name"
                    class="bg-white rounded-2xl p-8 shadow-xl shadow-emerald-100/50"
                >
                    <div class="flex items-center gap-1 text-amber-400 mb-4">
                        <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-slate-600 leading-relaxed mb-6">"{{ testimonial.text }}"</p>
                    <div class="flex items-center gap-4">
                        <img :src="testimonial.avatar" :alt="testimonial.name" class="w-12 h-12 rounded-full object-cover" />
                        <div>
                            <p class="font-semibold text-slate-900">{{ testimonial.name }}</p>
                            <p class="text-sm text-slate-500">Verified Pilgrim</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contact" class="py-24 bg-gradient-to-r from-emerald-600 to-teal-600">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Ready to Start Your Journey?
            </h2>
            <p class="text-xl text-white/80 mb-10">
                Get in touch with us today and let us help you plan your perfect pilgrimage
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a
                    href="https://wa.me/9607999999"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 bg-white text-emerald-600 font-semibold px-8 py-4 rounded-full hover:bg-emerald-50 transition shadow-xl"
                >
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat on WhatsApp
                </a>
                <a
                    href="tel:+9607999999"
                    class="inline-flex items-center justify-center gap-2 bg-white/20 text-white font-semibold px-8 py-4 rounded-full hover:bg-white/30 transition border border-white/30"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    Call +960 7999999
                </a>
            </div>
        </div>
    </section>
</template>
