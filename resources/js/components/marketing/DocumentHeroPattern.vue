<script setup lang="ts">
import {
    CheckCircle2,
    FileText,
    ReceiptText,
    ScanLine,
    Table2,
} from '@lucide/vue';
</script>

<template>
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-0 overflow-hidden"
    >
        <div
            class="absolute inset-0 [background-image:radial-gradient(circle_at_center,rgba(59,130,246,0.14)_0,transparent_58%)] opacity-30"
        />
        <div
            class="absolute inset-0 [background-image:linear-gradient(rgba(148,163,184,0.3)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.3)_1px,transparent_1px)] [background-size:48px_48px] opacity-[0.07]"
        />

        <svg
            viewBox="0 0 1440 420"
            preserveAspectRatio="none"
            class="absolute inset-0 hidden size-full opacity-20 md:block"
        >
            <path
                d="M64 118 C 220 118, 210 310, 382 310 M1058 104 C 1210 104, 1190 300, 1370 300"
                fill="none"
                stroke="rgb(96 165 250)"
                stroke-width="1.5"
                stroke-dasharray="7 10"
            />
            <circle cx="382" cy="310" r="4" fill="rgb(96 165 250)" />
            <circle cx="1058" cy="104" r="4" fill="rgb(96 165 250)" />
        </svg>

        <div
            class="absolute top-12 -left-5 hidden -rotate-6 rounded-2xl border border-white/10 bg-white/[0.035] p-4 text-blue-300 shadow-2xl shadow-blue-950/30 sm:block lg:left-[5%]"
        >
            <FileText class="size-8" :stroke-width="1.5" />
            <div class="mt-3 space-y-1.5">
                <span class="block h-1 w-16 rounded-full bg-white/15" />
                <span class="block h-1 w-11 rounded-full bg-white/10" />
            </div>
        </div>

        <div
            class="absolute bottom-7 -left-3 rotate-5 rounded-2xl border border-white/10 bg-white/[0.035] p-4 text-cyan-300 shadow-2xl shadow-blue-950/30 lg:bottom-9 lg:left-[8%]"
        >
            <ReceiptText class="size-7" :stroke-width="1.5" />
        </div>

        <div
            class="absolute top-10 -right-4 rotate-6 rounded-2xl border border-white/10 bg-white/[0.035] p-4 text-blue-300 shadow-2xl shadow-blue-950/30 sm:right-[3%] lg:right-[7%]"
        >
            <ScanLine class="size-8" :stroke-width="1.5" />
        </div>

        <div
            class="absolute right-[7%] bottom-8 hidden -rotate-4 items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.035] p-4 text-emerald-300 shadow-2xl shadow-blue-950/30 lg:flex"
        >
            <Table2 class="size-7 text-blue-300" :stroke-width="1.5" />
            <CheckCircle2 class="size-6" :stroke-width="1.5" />
        </div>
    </div>
</template>
