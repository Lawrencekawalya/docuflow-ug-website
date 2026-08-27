<script setup lang="ts">
import MarketingFooter from '@/components/marketing/MarketingFooter.vue';
import MarketingHeader from '@/components/marketing/MarketingHeader.vue';
</script>

<template>
    <div class="min-h-screen overflow-x-clip bg-white text-slate-950 antialiased">
        <a
            href="#main-content"
            class="sr-only z-[100] rounded-md bg-white px-4 py-2 text-blue-700 focus:not-sr-only focus:fixed focus:top-3 focus:left-3"
        >
            Skip to main content
        </a>
        <MarketingHeader />
        <main id="main-content">
            <slot />
        </main>
        <MarketingFooter />
    </div>
</template>
