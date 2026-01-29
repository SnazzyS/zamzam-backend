<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <Head title="ޒަމްޒަމް" />

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-violet-50 via-white to-slate-50">
        <div class="mx-auto max-w-6xl px-6 py-24 sm:py-32">
            <div class="text-center" dir="rtl" lang="dv">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                    މަރުޙަބާ
                    <span class="text-violet-600">ޒަމްޒަމް</span>
                    އަށް
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600">
                    ދިވެހިރާއްޖެއިން ޢުމްރާ އަދި ޙައްޖު ދަތުރުގެ ޚިދުމަތް ފޯރުކޮށްދިނުމުގައި އިތުބާރު ހިފޭ ބައިވެރިއެއް.
                    ތިޔަބޭފުޅުންގެ ރޫޙާނީ ދަތުރު ފަސޭހަ އަދި ހަނދާނުން ފޮހެނުލެވޭ ދަތުރަކަށް ހެދުމަށް އަޅުގަނޑުމެން މަސައްކަތް ކުރަން.
                </p>
                <div class="mt-10 flex items-center justify-center gap-4">
                    <a
                        href="#contact"
                        class="rounded-lg bg-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2"
                    >
                        ގުޅުއްވާ
                    </a>
                    <a
                        href="/about"
                        class="rounded-lg bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50"
                    >
                        އިތުރަށް ބައްލަވާ
                    </a>
                </div>
            </div>
        </div>

        <!-- Decorative elements -->
        <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-violet-100 opacity-50 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-violet-100 opacity-50 blur-3xl"></div>
    </section>

    <!-- ތަޢާރަފް Section -->
    <section id="thaaaruf" class="py-20">
        <div class="mx-auto max-w-6xl px-6">
            <div class="text-center" dir="rtl" lang="dv">
                <h2 class="text-3xl font-bold text-slate-900">ތަޢާރަފް</h2>
            </div>
            <!-- Content will be added later -->
        </div>
    </section>

    <!-- ޚިދުމަތްތައް Section -->
    <section id="khidhumaiy" class="bg-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-6">
            <div class="text-center" dir="rtl" lang="dv">
                <h2 class="text-3xl font-bold text-slate-900">ޚިދުމަތްތައް</h2>
            </div>
            <!-- Content will be added later -->
        </div>
    </section>

    <!-- ޑައުންލޯޑްސް Section -->
    <section id="downloads" class="py-20">
        <div class="mx-auto max-w-6xl px-6">
            <div class="text-center" dir="rtl" lang="dv">
                <h2 class="text-3xl font-bold text-slate-900">ޑައުންލޯޑްސް</h2>
            </div>
            <!-- Content will be added later -->
        </div>
    </section>
</template>
