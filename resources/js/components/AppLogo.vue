<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="flex aspect-square size-9 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-950/20"
    >
        <AppLogoIcon class="size-7" />
    </div>
    <div
        class="ml-1 grid flex-1 text-left group-data-[collapsible=icon]:hidden"
    >
        <span class="truncate text-sm leading-tight font-extrabold"
            >DocuFlow</span
        >
        <span
            class="mt-0.5 truncate text-[9px] leading-none font-bold tracking-[0.18em] text-blue-600 uppercase dark:text-blue-300"
            >Uganda Support</span
        >
    </div>
</template>
