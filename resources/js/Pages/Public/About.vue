<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <Head title="އަޅުގަނޑުމެން" />

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-violet-50 via-white to-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-6">
            <div class="text-center" dir="rtl" lang="dv">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">
                    އަޅުގަނޑުމެންނާ ބެހޭގޮތުން
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600">
                    ޒަމްޒަމް އާ ބެހޭގޮތުން އިތުރަށް ދެނެގަންނަވާ. ދިވެހިރާއްޖެއިން ޙައްޖާޖީންނަށް ޚިދުމަތް ކުރުމަށް އަޅުގަނޑުމެންގެ އަޒުމް.
                </p>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-20">
        <div class="mx-auto max-w-4xl px-6" dir="rtl" lang="dv">
            <div class="space-y-12">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">އަޅުގަނޑުމެންގެ ވާހަކަ</h2>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        ޒަމްޒަމް އުފެއްދީ އެއް މަޤްޞަދެއްގައި: ދިވެހިރާއްޖޭގެ މުސްލިމުންނަށް ޢުމްރާ އަދި ޙައްޖުގެ އަޅުކަން އަދާކުރުމުގެ ހުވަފެން ޙަޤީޤަތަކަށް ހެދުމަށް އެހީތެރިވުން.
                        މި ރޫޙާނީ ދަތުރުގެ މުހިންމުކަން އަޅުގަނޑުމެންނަށް އެނގޭ، އަދި ކޮންމެ ޙައްޖާޖީއަކަށް ވީހާވެސް ފަސޭހަ އަދި މާނަހުރި ދަތުރަކަށް ހެދުމަށް އަޅުގަނޑުމެން ކުރެވެން ހުރި ހުރިހާ މަސައްކަތެއް ކުރަން.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-slate-900">އަޅުގަނޑުމެންގެ މިޝަން</h2>
                    <p class="mt-4 text-slate-600 leading-relaxed">
                        ޙައްޖާޖީންނަށް އެބޭފުޅުންގެ ރޫޙާނީ ތަޖުރިބާއަށް މުޅިން ސަމާލުކަން ދެވޭނެ ފެންވަރުގެ ފުރިހަމަ ޢުމްރާ އަދި ޙައްޖު ޚިދުމަތް ފޯރުކޮށްދިނުން.
                        ތިޔަބޭފުޅުންނަށް ހަމައެކަނި މުހިންމު ކަމަކަށް - ﷲ އާ އޮންނަ ގުޅުމަށް - ސަމާލުކަން ދެވޭނެހެން ހުރިހާ ލޮޖިސްޓިކްސް ކަމެއް އަޅުގަނޑުމެން ބަލަހައްޓައިދެން.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-slate-900">އަޅުގަނޑުމެން ފޯރުކޮށްދޭ ޚިދުމަތް</h2>
                    <ul class="mt-4 space-y-3 text-slate-600">
                        <li class="flex items-start gap-3">
                            <svg class="mt-1 h-5 w-5 flex-shrink-0 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>ފްލައިޓް އަދި ޓްރާންސްފާ ހިމެނޭގޮތަށް ފުރިހަމަ ދަތުރު އިންތިޒާމް</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-1 h-5 w-5 flex-shrink-0 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>މައްކާ އަދި މަދީނާގެ ޙަރަމާ ކައިރި ހޮޓެލް</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-1 h-5 w-5 flex-shrink-0 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>ތަޖުރިބާކާރު ގައިޑުން އަދި ދީނީ ޢިލްމުވެރިން</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-1 h-5 w-5 flex-shrink-0 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>ވިސާ ޕްރޮސެސިންގ އަދި ލިޔެކިޔުމުގެ އެހީތެރިކަން</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-1 h-5 w-5 flex-shrink-0 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>ދަތުރުގެ އެކި މަރުޙަލާތަކުގައި 24/7 ސަޕޯޓް</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>
