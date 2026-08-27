<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, CheckCircle2, CircleAlert, Clock3, Eye, Gauge, KeyRound, ShieldCheck, Sparkles } from 'lucide-vue-next';
import DemoCta from '@/components/marketing/DemoCta.vue';
import SectionHeading from '@/components/marketing/SectionHeading.vue';
import WorkflowVisual from '@/components/marketing/WorkflowVisual.vue';
import { contact, howItWorks } from '@/routes';

const problems = [
    { icon: Clock3, title: 'Hours lost to manual entry', text: 'Staff repeatedly read documents and copy information into spreadsheets or business systems.' },
    { icon: CircleAlert, title: 'Costly human errors', text: 'Invoice numbers, prices, dates and totals can be entered incorrectly when teams are under pressure.' },
    { icon: Gauge, title: 'Slow processing', text: 'Documents pass through unnecessary manual steps before the right person can use the information.' },
    { icon: Eye, title: 'Poor visibility', text: 'It becomes difficult to know what was processed, what failed and what still needs attention.' },
];

const benefits = [
    ['Save staff time', 'Reduce repetitive reading and data-entry work so your team can focus on higher-value tasks.'],
    ['Reduce manual errors', 'Consistent, structured extraction helps reduce avoidable copying mistakes.'],
    ['Process faster', 'Move documents through an agreed workflow without unnecessary manual handoffs.'],
    ['Keep visibility', 'Monitor processing status and failures instead of letting documents disappear silently.'],
];

const stats = [
    ['5', 'businesses interviewed'], ['3.8/5', 'average automation interest'],
    ['5/5', 'reported monthly document problems'], ['4/5', 'expressed potential paying interest'],
];
</script>

<template>
    <Head title="Document Automation for Ugandan Businesses">
        <meta name="description" content="DocuFlow UG extracts, validates and organizes information from invoices, receipts and business documents—reducing manual data entry for Ugandan teams." />
    </Head>

    <section class="relative overflow-hidden bg-slate-50 px-5 pt-16 pb-20 sm:px-6 sm:pt-20 lg:px-8 lg:pt-24 lg:pb-28">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_15%,rgba(59,130,246,0.13),transparent_32%)]" />
        <div class="relative mx-auto grid max-w-7xl items-center gap-14 lg:grid-cols-[1.08fr_0.92fr]">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm"><Sparkles class="size-3.5" aria-hidden="true" /> Practical automation for document-heavy teams</div>
                <h1 class="mt-6 max-w-4xl text-4xl leading-[1.08] font-extrabold tracking-[-0.045em] text-balance text-slate-950 sm:text-5xl lg:text-6xl">Turn business documents into <span class="text-blue-600">ready-to-use data</span> — automatically</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">DocuFlow UG helps accounting firms and document-heavy businesses reduce manual data entry by extracting, validating and organizing information from invoices, receipts and other business documents.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <Link :href="contact()" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">Request a Free Demo <ArrowRight class="size-4" aria-hidden="true" /></Link>
                    <Link :href="howItWorks()" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 bg-white px-6 font-bold text-slate-800 transition hover:border-blue-300 hover:text-blue-700">See How It Works</Link>
                </div>
                <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm font-semibold text-slate-600"><span class="inline-flex items-center gap-2"><CheckCircle2 class="size-4 text-emerald-600" /> Built around real workflows</span><span class="inline-flex items-center gap-2"><CheckCircle2 class="size-4 text-emerald-600" /> Human review stays possible</span></div>
            </div>
            <WorkflowVisual />
        </div>
    </section>

    <section class="px-5 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="mx-auto max-w-7xl">
            <SectionHeading eyebrow="The problem" title="Still processing business documents by hand?" description="Manual document handling quietly consumes working hours, introduces errors and makes every handoff harder to track." centered />
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <article v-for="problem in problems" :key="problem.title" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"><span class="grid size-11 place-items-center rounded-xl bg-blue-50 text-blue-700"><component :is="problem.icon" class="size-5" aria-hidden="true" /></span><h3 class="mt-5 text-lg font-extrabold text-slate-950">{{ problem.title }}</h3><p class="mt-3 text-sm leading-7 text-slate-600">{{ problem.text }}</p></article>
            </div>
            <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 px-6 py-5 text-center text-sm leading-7 text-blue-950">In our initial research, <strong>all five businesses interviewed</strong> reported document-processing errors or operational problems occurring monthly.</div>
        </div>
    </section>

    <section class="bg-slate-950 px-5 py-20 text-white sm:px-6 lg:px-8 lg:py-28">
        <div class="mx-auto grid max-w-7xl gap-14 lg:grid-cols-2 lg:items-center">
            <div><p class="text-sm font-extrabold tracking-[0.14em] text-blue-400 uppercase">A practical solution</p><h2 class="mt-3 text-3xl font-extrabold tracking-[-0.035em] text-balance sm:text-4xl">Let DocuFlow handle the repetitive work</h2><p class="mt-5 text-lg leading-8 text-slate-300">Documents are received through an agreed intake process, read for the information your team needs, checked and structured, then delivered to the destination where work continues.</p><Link :href="howItWorks()" class="mt-7 inline-flex items-center gap-2 font-bold text-blue-300 hover:text-blue-200">Explore the five-step process <ArrowRight class="size-4" /></Link></div>
            <ol class="grid gap-3"><li v-for="(label, index) in ['Receive', 'Read', 'Extract', 'Validate', 'Deliver']" :key="label" class="flex items-center gap-4 rounded-2xl border border-slate-800 bg-slate-900 p-4"><span class="grid size-10 place-items-center rounded-xl bg-blue-600 text-sm font-extrabold">{{ index + 1 }}</span><span class="font-bold">{{ label }}</span><span class="ml-auto text-sm text-slate-500">{{ index === 4 ? 'Ready to use' : 'Next' }}</span></li></ol>
        </div>
    </section>

    <section class="bg-slate-50 px-5 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="mx-auto max-w-7xl"><SectionHeading eyebrow="Business benefits" title="Less typing. Better visibility. Faster work." description="DocuFlow supports your team with a controlled process—not a black box that replaces important decisions." centered /><div class="mt-12 grid gap-5 md:grid-cols-2"><article v-for="([title, text], index) in benefits" :key="title" class="flex gap-5 rounded-2xl border border-slate-200 bg-white p-6"><span class="grid size-10 shrink-0 place-items-center rounded-full bg-emerald-50 font-extrabold text-emerald-700">{{ index + 1 }}</span><div><h3 class="font-extrabold text-slate-950">{{ title }}</h3><p class="mt-2 text-sm leading-7 text-slate-600">{{ text }}</p></div></article></div></div>
    </section>

    <section class="px-5 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="mx-auto max-w-7xl"><SectionHeading eyebrow="Early customer research" title="Built around real business needs" description="Five interviews in Mbarara shaped our focus on accuracy, security, reliability, processing time and evidence before commitment." centered /><div class="mt-12 grid grid-cols-2 gap-4 lg:grid-cols-4"><div v-for="([value, label]) in stats" :key="label" class="rounded-2xl border border-slate-200 p-5 text-center sm:p-7"><p class="text-3xl font-extrabold tracking-tight text-blue-600 sm:text-4xl">{{ value }}</p><p class="mt-2 text-xs leading-5 font-semibold text-slate-600 sm:text-sm">{{ label }}</p></div></div><p class="mx-auto mt-5 max-w-3xl text-center text-xs leading-5 text-slate-500">These figures report stated interest from a small initial research sample. They are not customer-count, accuracy or sales claims.</p></div>
    </section>

    <section class="bg-blue-50 px-5 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-10 rounded-3xl border border-blue-100 bg-white p-7 sm:p-10 lg:grid-cols-[auto_1fr] lg:p-12"><span class="grid size-14 place-items-center rounded-2xl bg-blue-600 text-white"><ShieldCheck class="size-7" /></span><div><h2 class="text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Your business data deserves careful handling</h2><p class="mt-4 max-w-4xl leading-8 text-slate-600">DocuFlow is designed around controlled access, secure credential handling, input validation, monitored processing and visible failure paths. We agree how information should move before automation begins.</p><div class="mt-6 flex flex-wrap gap-3 text-sm font-bold text-slate-700"><span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-2"><KeyRound class="size-4 text-blue-600" /> Controlled credentials</span><span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-2"><Eye class="size-4 text-blue-600" /> Monitored processing</span></div></div></div>
    </section>

    <DemoCta />
</template>
