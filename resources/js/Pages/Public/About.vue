<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

const team = [
    { name: 'އަޙްމަދު', role: 'މެނޭޖިންގ ޑައިރެކްޓަރ' },
    { name: 'މުޙައްމަދު', role: 'އޮޕަރޭޝަންސް މެނޭޖަރ' },
    { name: 'ޢަލީ', role: 'ކަސްޓަމަރ ސަރވިސް' },
];
</script>

<template>
    <Head title="އަޅުގަނޑުމެން - ޒަމްޒަމް" />

    <!-- Hero Section -->
    <section class="bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6">
            <div class="max-w-3xl mx-auto text-center" dir="rtl" lang="dv">
                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">
                    އަޅުގަނޑުމެންނާ ބެހޭގޮތުން
                </h1>
                <p class="mt-4 text-lg text-slate-600">
                    ޒަމްޒަމް އާ ބެހޭގޮތުން އިތުރަށް ދެނެގަންނަވާ
                </p>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="py-16 bg-slate-50">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="flex justify-center">
                    <img
                        src="/images/logo.png"
                        alt="Zam Zam Logo"
                        class="h-48 sm:h-64 w-auto"
                    />
                </div>
                <div dir="rtl" lang="dv">
                    <h2 class="text-2xl font-bold text-slate-900">އަޅުގަނޑުމެންގެ ވާހަކަ</h2>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        ޒަމްޒަމް އުފެއްދީ އެއް މަޤްޞަދެއްގައި: ދިވެހިރާއްޖޭގެ މުސްލިމުންނަށް ޢުމްރާ އަދި ޙައްޖުގެ އަޅުކަން އަދާކުރުމުގެ ހުވަފެން ޙަޤީޤަތަކަށް ހެދުމަށް އެހީތެރިވުން.
                    </p>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        މި ރޫޙާނީ ދަތުރުގެ މުހިންމުކަން އަޅުގަނޑުމެންނަށް އެނގޭ، އަދި ކޮންމެ ޙައްޖާޖީއަކަށް ވީހާވެސް ފަސޭހަ އަދި މާނަހުރި ދަތުރަކަށް ހެދުމަށް އަޅުގަނޑުމެން ކުރެވެން ހުރި ހުރިހާ މަސައްކަތެއް ކުރަން.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-16 bg-white">
        <div class="mx-auto max-w-7xl px-6">
            <div class="max-w-3xl mx-auto" dir="rtl" lang="dv">
                <h2 class="text-2xl font-bold text-slate-900 text-center">އަޅުގަނޑުމެންގެ މިޝަން</h2>
                <p class="mt-6 text-slate-600 leading-relaxed text-center">
                    ޙައްޖާޖީންނަށް އެބޭފުޅުންގެ ރޫޙާނީ ތަޖުރިބާއަށް މުޅިން ސަމާލުކަން ދެވޭނެ ފެންވަރުގެ ފުރިހަމަ ޢުމްރާ އަދި ޙައްޖު ޚިދުމަތް ފޯރުކޮށްދިނުން.
                    ތިޔަބޭފުޅުންނަށް ހަމައެކަނި މުހިންމު ކަމަކަށް - ﷲ އާ އޮންނަ ގުޅުމަށް - ސަމާލުކަން ދެވޭނެހެން ހުރިހާ ލޮޖިސްޓިކްސް ކަމެއް އަޅުގަނޑުމެން ބަލަހައްޓައިދެން.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 bg-teal-600">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center mb-12" dir="rtl" lang="dv">
                <h2 class="text-2xl font-bold text-white">އަޅުގަނޑުމެން ފޯރުކޮށްދޭ ޚިދުމަތް</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" dir="rtl" lang="dv">
                <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-white font-medium">ފުރިހަމަ ދަތުރު އިންތިޒާމް</span>
                    </div>
                    <p class="text-teal-100 text-sm">ފްލައިޓް އަދި ޓްރާންސްފާ ހިމެނޭގޮތަށް</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-white font-medium">ޙަރަމާ ކައިރި ހޮޓެލް</span>
                    </div>
                    <p class="text-teal-100 text-sm">މައްކާ އަދި މަދީނާގައި</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-white font-medium">ތަޖުރިބާކާރު ގައިޑުން</span>
                    </div>
                    <p class="text-teal-100 text-sm">ދީނީ ޢިލްމުވެރިންނާއެކު</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-white font-medium">ވިސާ ޕްރޮސެސިންގ</span>
                    </div>
                    <p class="text-teal-100 text-sm">ލިޔެކިޔުމުގެ އެހީތެރިކަން</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-white font-medium">24/7 ސަޕޯޓް</span>
                    </div>
                    <p class="text-teal-100 text-sm">ދަތުރުގެ އެކި މަރުޙަލާތަކުގައި</p>
                </div>

                <div class="bg-white/10 backdrop-blur rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-white font-medium">ތަޢުލީމީ ޕްރޮގްރާމް</span>
                    </div>
                    <p class="text-teal-100 text-sm">ޙައްޖު އަދި ޢުމްރާގެ ތަޢުލީމް</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-slate-50">
        <div class="mx-auto max-w-7xl px-6">
            <div class="text-center" dir="rtl" lang="dv">
                <h2 class="text-2xl font-bold text-slate-900">
                    ތިޔަބޭފުޅުންގެ ދަތުރު ފެށުމަށް ތައްޔާރުތޯ؟
                </h2>
                <p class="mt-3 text-slate-600">
                    އިތުރު މަޢުލޫމާތު ހޯއްދެވުމަށް އަޅުގަނޑުމެންނާ ގުޅުއްވާ
                </p>
                <div class="mt-8">
                    <a
                        href="/#contact"
                        class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-teal-700"
                    >
                        ގުޅުއްވާ
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
