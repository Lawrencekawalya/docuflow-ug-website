<script setup lang="ts">
withDefaults(
    defineProps<{
        compact?: boolean;
        inverted?: boolean;
    }>(),
    {
        compact: false,
        inverted: false,
    },
);
</script>

<template>
    <span class="inline-flex items-center gap-2.5" aria-label="DocuFlow UG">
        <span
            class="relative grid size-10 shrink-0 place-items-center overflow-hidden rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/25"
        >
            <svg viewBox="0 0 40 40" class="size-8" aria-hidden="true">
                <path
                    d="M10 8.5h11l7 7V31.5H10z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.3"
                    stroke-linejoin="round"
                />
                <path
                    d="M21 8.5v7h7M14.5 21h9M14.5 25.5h6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.3"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
                <path
                    d="m23.5 28 2.1 2.1 4.4-5"
                    fill="none"
                    stroke="#86efac"
                    stroke-width="2.4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </span>
        <span v-if="!compact" class="flex flex-col leading-none">
            <span
                class="text-[1.05rem] font-extrabold tracking-[-0.03em]"
                :class="inverted ? 'text-white' : 'text-slate-950'"
            >
                DocuFlow
            </span>
            <span
                class="mt-1 text-[0.65rem] font-bold tracking-[0.2em] uppercase"
                :class="inverted ? 'text-blue-200' : 'text-blue-600'"
            >
                Uganda
            </span>
        </span>
    </span>
</template>
