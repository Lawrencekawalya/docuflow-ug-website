<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import { contact } from '@/routes';

withDefaults(defineProps<{ title?: string; description?: string }>(), {
    title: 'See what DocuFlow could automate for your business',
    description:
        'Show us a realistic document workflow. We’ll help you identify where automation could save time and reduce repetitive work.',
});
</script>

<template>
    <section class="px-5 py-20 sm:px-6 lg:px-8">
        <div
            class="relative mx-auto max-w-7xl overflow-hidden rounded-3xl bg-blue-600 px-6 py-12 text-white shadow-2xl shadow-blue-950/15 sm:px-10 lg:flex lg:items-center lg:justify-between lg:px-14"
        >
            <div
                class="absolute -top-24 -right-16 size-72 rounded-full bg-cyan-400/20 blur-2xl"
            />
            <div class="relative max-w-3xl">
                <p
                    class="text-sm font-bold tracking-[0.15em] text-blue-100 uppercase"
                >
                    Your next step
                </p>
                <h2
                    class="mt-3 text-3xl font-extrabold tracking-[-0.035em] text-balance sm:text-4xl"
                >
                    {{ title }}
                </h2>
                <p class="mt-4 max-w-2xl leading-7 text-blue-100">
                    {{ description }}
                </p>
            </div>
            <Link
                :href="contact()"
                class="relative mt-8 inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-6 font-bold text-blue-700 shadow-lg transition hover:bg-blue-50 lg:mt-0"
            >
                Request a Free Demo
                <ArrowRight class="size-4" aria-hidden="true" />
            </Link>
        </div>
    </section>
</template>
