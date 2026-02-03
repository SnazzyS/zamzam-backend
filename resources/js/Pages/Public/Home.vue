<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

const services = [
    {
        title: 'ޢުމްރާ ދަތުރު',
        description: 'ރަމަޟާން މަހާއި އެހެން މޫސުމްތަކުގައި ޢުމްރާ ދަތުރު',
        icon: 'kaaba',
    },
    {
        title: 'ޙައްޖު ދަތުރު',
        description: 'ފުރިހަމަ ޙައްޖު ޕެކޭޖް ހުރިހާ ޚިދުމަތަކާއެކު',
        icon: 'hajj',
    },
    {
        title: 'ހޮޓެލް ބުކިންގ',
        description: 'ޙަރަމްފުޅާ ކައިރި ހޮޓެލް',
        icon: 'hotel',
    },
    {
        title: 'ވިސާ ޚިދުމަތް',
        description: 'ވިސާ ޕްރޮސެސިންގ އެހީތެރިކަން',
        icon: 'visa',
    },
];

const stats = [
    { value: '15+', label: 'އަހަރުގެ ތަޖުރިބާ' },
    { value: '5000+', label: 'ޙައްޖާޖީން' },
    { value: '50+', label: 'ދަތުރު' },
];
</script>

<template>
    <Head title="ޒަމްޒަމް - ޙައްޖު އެންޑް ޢުމްރާ" />

    <!-- Hero Section -->
    <section class="relative bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div class="text-center lg:text-right order-2 lg:order-1" dir="rtl" lang="dv">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                        މަރުޙަބާ
                        <span class="text-teal-600">ޒަމްޒަމް</span>
                        އަށް
                    </h1>
                    <p class="mt-6 text-lg text-slate-600 leading-relaxed">
                        ދިވެހިރާއްޖެއިން ޢުމްރާ އަދި ޙައްޖު ދަތުރުގެ ޚިދުމަތް ފޯރުކޮށްދިނުމުގައި އިތުބާރު ހިފޭ ބައިވެރިއެއް.
                        ތިޔަބޭފުޅުންގެ ރޫޙާނީ ދަތުރު ފަސޭހަ އަދި ހަނދާނުން ފޮހެނުލެވޭ ދަތުރަކަށް ހެދުމަށް އަޅުގަނޑުމެން މަސައްކަތް ކުރަން.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a
                            href="#contact"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-teal-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700"
                        >
                            ގުޅުއްވާ
                        </a>
                        <a
                            href="#services"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50"
                        >
                            ޚިދުމަތްތައް ބައްލަވާ
                        </a>
                    </div>
                </div>

                <!-- Logo/Image -->
                <div class="flex justify-center order-1 lg:order-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-teal-100 rounded-full blur-3xl opacity-30"></div>
                        <img
                            src="/images/logo.png"
                            alt="Zam Zam Logo"
                            class="relative h-48 sm:h-64 lg:h-80 w-auto"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-teal-600 py-12">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-3 gap-8" dir="rtl" lang="dv">
                <div v-for="stat in stats" :key="stat.label" class="text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-white" style="font-family: ui-sans-serif, system-ui, sans-serif;">
                        {{ stat.value }}
                    </p>
                    <p class="mt-1 text-sm text-teal-100">{{ stat.label }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 sm:py-24 bg-slate-50">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-12" dir="rtl" lang="dv">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">ޚިދުމަތްތައް</h2>
                <p class="mt-3 text-slate-600">އަޅުގަނޑުމެން ފޯރުކޮށްދޭ ޚިދުމަތްތައް</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6" dir="rtl" lang="dv">
                <div
                    v-for="service in services"
                    :key="service.title"
                    class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="h-12 w-12 rounded-lg bg-teal-50 flex items-center justify-center mb-4">
                        <svg v-if="service.icon === 'kaaba'" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        <svg v-else-if="service.icon === 'hajj'" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <svg v-else-if="service.icon === 'hotel'" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        <svg v-else-if="service.icon === 'visa'" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ service.title }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ service.description }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 sm:py-24 bg-white">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center" dir="rtl" lang="dv">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">އަޅުގަނޑުމެންނާ ބެހޭގޮތުން</h2>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        ޒަމްޒަމް އުފެއްދީ އެއް މަޤްޞަދެއްގައި: ދިވެހިރާއްޖޭގެ މުސްލިމުންނަށް ޢުމްރާ އަދި ޙައްޖުގެ އަޅުކަން އަދާކުރުމުގެ ހުވަފެން ޙަޤީޤަތަކަށް ހެދުމަށް އެހީތެރިވުން.
                    </p>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        މި ރޫޙާނީ ދަތުރުގެ މުހިންމުކަން އަޅުގަނޑުމެންނަށް އެނގޭ، އަދި ކޮންމެ ޙައްޖާޖީއަކަށް ވީހާވެސް ފަސޭހަ އަދި މާނަހުރި ދަތުރަކަށް ހެދުމަށް އަޅުގަނޑުމެން ކުރެވެން ހުރި ހުރިހާ މަސައްކަތެއް ކުރަން.
                    </p>
                    <ul class="mt-6 space-y-3">
                        <li class="flex items-center gap-3 text-slate-600">
                            <svg class="h-5 w-5 text-teal-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>ތަޖުރިބާކާރު ޓީމެއް</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-600">
                            <svg class="h-5 w-5 text-teal-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>24/7 ސަޕޯޓް</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-600">
                            <svg class="h-5 w-5 text-teal-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>އެންމެ ރަނގަޅު އަގު</span>
                        </li>
                    </ul>
                </div>
                <div class="flex justify-center">
                    <img
                        src="/images/logo.png"
                        alt="Zam Zam"
                        class="h-48 sm:h-64 w-auto opacity-20"
                    />
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 sm:py-24 bg-slate-900">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center" dir="rtl" lang="dv">
                <h2 class="text-2xl sm:text-3xl font-bold text-white">ގުޅުއްވާ</h2>
                <p class="mt-3 text-slate-400">އިތުރު މަޢުލޫމާތު ހޯއްދެވުމަށް</p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a
                        href="tel:+9607999999"
                        class="inline-flex items-center gap-3 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                    >
                        <svg class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span style="font-family: ui-sans-serif, system-ui, sans-serif;">7999999</span>
                    </a>
                    <a
                        href="https://wa.me/9607999999"
                        target="_blank"
                        class="inline-flex items-center gap-3 rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-green-700"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
