<script setup lang="ts">
withDefaults(
    defineProps<{
        eyebrow?: string;
        title: string;
        description?: string;
        centered?: boolean;
    }>(),
    { eyebrow: '', description: '', centered: false },
);
</script>

<template>
    <div :class="['max-w-2xl', centered && 'mx-auto text-center']">
        <p v-if="eyebrow" class="text-sm font-extrabold tracking-[0.14em] text-blue-600 uppercase">
            {{ eyebrow }}
        </p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-[-0.035em] text-balance text-slate-950 sm:text-4xl">
            {{ title }}
        </h2>
        <p v-if="description" class="mt-5 text-base leading-8 text-slate-600 sm:text-lg">
            {{ description }}
        </p>
    </div>
</template>
