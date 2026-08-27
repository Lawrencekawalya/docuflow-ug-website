<script setup lang="ts">
import { Check, Database, FileText, ScanText, Send } from '@lucide/vue';

const steps = [
    {
        label: 'Document received',
        icon: FileText,
        tone: 'bg-blue-600 text-white',
    },
    {
        label: 'Important fields read',
        icon: ScanText,
        tone: 'bg-violet-100 text-violet-700',
    },
    {
        label: 'Information checked',
        icon: Check,
        tone: 'bg-emerald-100 text-emerald-700',
    },
    {
        label: 'Clean data prepared',
        icon: Database,
        tone: 'bg-cyan-100 text-cyan-700',
    },
    {
        label: 'Sent to your workflow',
        icon: Send,
        tone: 'bg-amber-100 text-amber-700',
    },
];
</script>

<template>
    <div
        class="relative overflow-hidden rounded-3xl border border-blue-100 bg-white p-4 shadow-2xl shadow-blue-950/10 sm:p-6"
        role="img"
        aria-label="A document moves through reading, validation and structured-data delivery"
    >
        <div
            class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4"
        >
            <div>
                <p
                    class="text-xs font-bold tracking-[0.15em] text-blue-600 uppercase"
                >
                    Live workflow
                </p>
                <p class="mt-1 text-sm font-bold text-slate-900">
                    Invoice processing
                </p>
            </div>
            <span
                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700"
            >
                <span class="size-1.5 rounded-full bg-emerald-500" /> Monitored
            </span>
        </div>
        <ol class="grid gap-3">
            <li
                v-for="(step, index) in steps"
                :key="step.label"
                class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/80 p-3"
            >
                <span
                    :class="[
                        'grid size-10 shrink-0 place-items-center rounded-xl',
                        step.tone,
                    ]"
                >
                    <component
                        :is="step.icon"
                        class="size-5"
                        aria-hidden="true"
                    />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-400">
                        Step {{ index + 1 }}
                    </p>
                    <p class="truncate text-sm font-bold text-slate-800">
                        {{ step.label }}
                    </p>
                </div>
                <Check
                    v-if="index < 4"
                    class="size-4 text-emerald-500"
                    aria-hidden="true"
                />
                <span
                    v-else
                    class="rounded-full bg-blue-100 px-2 py-1 text-[0.65rem] font-bold text-blue-700"
                    >Ready</span
                >
            </li>
        </ol>
    </div>
</template>
