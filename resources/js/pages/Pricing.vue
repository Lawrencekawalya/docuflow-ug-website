<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Check, CircleDollarSign, HelpCircle } from '@lucide/vue';
import DemoCta from '@/components/marketing/DemoCta.vue';
import DocumentHeroPattern from '@/components/marketing/DocumentHeroPattern.vue';
import SectionHeading from '@/components/marketing/SectionHeading.vue';
import { contact } from '@/routes';
import type { DocuflowPublicConfig, PricingPlanConfig } from '@/types';

const pricingConfig = (usePage().props.docuflow as DocuflowPublicConfig)
    .pricing;

const formatUgx = (value: number | string): string => {
    const amount = Number(value);

    return Number.isFinite(amount)
        ? `UGX ${new Intl.NumberFormat('en-UG').format(amount)}`
        : String(value);
};

const plans: Array<{
    name: string;
    description: string;
    config: PricingPlanConfig;
    featured?: boolean;
    features: string[];
}> = [
    {
        name: 'Starter',
        description: 'For smaller teams beginning with one document workflow.',
        config: pricingConfig.starter,
        featured: true,
        features: [
            'One automated document workflow',
            'AI-assisted extraction',
            'Structured output',
            'Basic monitoring',
            'Standard support',
        ],
    },
    {
        name: 'Growth',
        description: 'For teams handling larger recurring document volumes.',
        config: pricingConfig.growth,
        features: [
            'Additional document capacity',
            'Additional workflow integration',
            'Enhanced reporting',
            'Priority support',
            'Guided scaling review',
        ],
    },
    {
        name: 'Professional',
        description: 'For higher-volume or more customized operations.',
        config: pricingConfig.professional,
        features: [
            'Multiple workflows',
            'Higher document capacity',
            'Advanced integrations',
            'Operational reporting',
            'Priority onboarding and support',
        ],
    },
];

const included = [
    'Secure credential handling',
    'Workflow monitoring',
    'Error handling',
    'AI usage awareness',
    'Initial onboarding',
    'Ongoing support',
];
const faqs = [
    [
        'Why is there a setup fee?',
        'Every business handles documents differently. Setup covers workflow discovery, configuration, testing, integration and onboarding.',
    ],
    [
        'Can I change plans?',
        'Yes. Your plan can be adjusted as document volume or workflow requirements change.',
    ],
    [
        'What happens if I exceed my document allowance?',
        pricingConfig.terms.overage,
    ],
    ['Do I have to sign a long contract?', pricingConfig.terms.cancellation],
];
</script>

<template>
    <Head title="Pricing"
        ><meta
            name="description"
            content="Explore transparent UGX pricing for DocuFlow UG document automation, including setup, monitoring and support."
    /></Head>
    <section
        class="relative overflow-hidden bg-slate-950 px-5 py-20 text-white sm:px-6 lg:px-8 lg:py-28"
    >
        <DocumentHeroPattern />
        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p
                class="text-sm font-extrabold tracking-[0.14em] text-blue-400 uppercase"
            >
                Simple UGX pricing
            </p>
            <h1
                class="mt-4 text-4xl font-extrabold tracking-[-0.045em] text-balance sm:text-5xl lg:text-6xl"
            >
                Start at a level that fits your document volume
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-lg leading-8 text-slate-300">
                Service pricing reflects document volume, workflow complexity
                and the support your team needs. Costs are agreed clearly before
                implementation begins.
            </p>
        </div>
    </section>

    <section class="px-5 py-20 sm:px-6 lg:px-8 lg:py-28">
        <div class="mx-auto max-w-7xl">
            <div class="grid gap-6 lg:grid-cols-3">
                <article
                    v-for="plan in plans"
                    :key="plan.name"
                    :class="[
                        'relative flex flex-col rounded-3xl border p-7',
                        plan.featured
                            ? 'border-blue-500 shadow-xl shadow-blue-950/10'
                            : 'border-slate-200',
                    ]"
                >
                    <span
                        v-if="plan.featured"
                        class="absolute -top-3 left-6 rounded-full bg-blue-600 px-3 py-1 text-xs font-extrabold text-white"
                        >Interview-informed launch offer</span
                    >
                    <h2 class="text-2xl font-extrabold text-slate-950">
                        {{ plan.name }}
                    </h2>
                    <p class="mt-3 min-h-14 text-sm leading-7 text-slate-600">
                        {{ plan.description }}
                    </p>
                    <div class="mt-6 border-y border-slate-100 py-6">
                        <p
                            class="text-3xl font-extrabold tracking-tight text-slate-950"
                        >
                            {{ formatUgx(plan.config.monthly) }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500">per month</p>
                        <p class="mt-4 text-sm font-bold text-slate-800">
                            Setup: {{ formatUgx(plan.config.setup) }}
                        </p>
                        <p class="mt-2 text-sm text-slate-600">
                            Allowance:
                            {{ plan.config.allowance }} documents/month
                        </p>
                    </div>
                    <ul class="mt-6 grid gap-3 text-sm text-slate-700">
                        <li
                            v-for="feature in plan.features"
                            :key="feature"
                            class="flex gap-2.5"
                        >
                            <Check
                                class="mt-0.5 size-4 shrink-0 text-emerald-600"
                            />{{ feature }}
                        </li>
                    </ul>
                    <Link
                        :href="contact()"
                        :class="[
                            'mt-8 inline-flex min-h-12 items-center justify-center rounded-xl px-5 font-bold',
                            plan.featured
                                ? 'bg-blue-600 text-white hover:bg-blue-700'
                                : 'bg-slate-100 text-slate-900 hover:bg-slate-200',
                        ]"
                        >Start With a Demo</Link
                    >
                </article>
            </div>
        </div>
    </section>

    <section class="bg-blue-50 px-5 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <SectionHeading
                eyebrow="Included from day one"
                title="Every plan includes the operational basics"
                centered
            />
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="item in included"
                    :key="item"
                    class="flex items-center gap-3 rounded-2xl bg-white p-5 font-bold text-slate-800 shadow-sm"
                >
                    <span
                        class="grid size-8 place-items-center rounded-full bg-emerald-50"
                        ><Check class="size-4 text-emerald-600" /></span
                    >{{ item }}
                </div>
            </div>
        </div>
    </section>

    <section class="px-5 py-20 sm:px-6 lg:px-8">
        <div
            class="mx-auto grid max-w-7xl gap-10 rounded-3xl border border-slate-200 p-8 lg:grid-cols-[auto_1fr_auto] lg:items-center lg:p-12"
        >
            <span
                class="grid size-14 place-items-center rounded-2xl bg-blue-50 text-blue-700"
                ><CircleDollarSign class="size-7"
            /></span>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-950">
                    Not ready to commit? Start with a demonstration.
                </h2>
                <p class="mt-3 leading-7 text-slate-600">
                    Evaluate DocuFlow against a realistic workflow so your team
                    can assess accuracy, processing time and fit before making a
                    longer-term decision.
                </p>
            </div>
            <Link
                :href="contact()"
                class="inline-flex min-h-12 items-center justify-center rounded-xl bg-blue-600 px-6 font-bold text-white"
                >Request Demo</Link
            >
        </div>
    </section>

    <section class="bg-slate-50 px-5 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <SectionHeading
                eyebrow="Pricing FAQ"
                title="Questions before you begin"
                centered
            />
            <div class="mt-10 grid gap-3">
                <details
                    v-for="[question, answer] in faqs"
                    :key="question"
                    class="group rounded-2xl border border-slate-200 bg-white p-5"
                >
                    <summary
                        class="flex cursor-pointer list-none items-center justify-between gap-4 font-extrabold text-slate-950"
                    >
                        <span>{{ question }}</span
                        ><HelpCircle class="size-5 shrink-0 text-blue-600" />
                    </summary>
                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">
                        {{ answer }}
                    </p>
                </details>
            </div>
        </div>
    </section>
    <DemoCta />
</template>
