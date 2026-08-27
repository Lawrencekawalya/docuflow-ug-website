<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import BrandMark from '@/components/marketing/BrandMark.vue';
import { about, contact, home, howItWorks, pricing } from '@/routes';

const menuOpen = ref(false);
const currentPath = computed(() => usePage().url.split('?')[0]);

const navigation = [
    { label: 'Home', href: home(), path: '/' },
    { label: 'How It Works', href: howItWorks(), path: '/how-it-works' },
    { label: 'Pricing', href: pricing(), path: '/pricing' },
    { label: 'About', href: about(), path: '/about' },
    { label: 'Contact', href: contact(), path: '/contact' },
];
</script>

<template>
    <header
        class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl"
    >
        <div
            class="mx-auto flex h-18 max-w-7xl items-center justify-between px-5 sm:px-6 lg:px-8"
        >
            <Link :href="home()" aria-label="DocuFlow UG home">
                <BrandMark />
            </Link>

            <nav
                class="hidden items-center gap-1 lg:flex"
                aria-label="Primary navigation"
            >
                <Link
                    v-for="item in navigation"
                    :key="item.path"
                    :href="item.href"
                    class="rounded-lg px-3.5 py-2 text-sm font-semibold transition-colors"
                    :class="
                        currentPath === item.path
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'
                    "
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="flex items-center gap-3">
                <Link
                    :href="contact()"
                    class="hidden rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-blue-600/20 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:inline-flex"
                >
                    Request Demo
                </Link>
                <button
                    type="button"
                    class="grid size-11 place-items-center rounded-xl border border-slate-200 text-slate-800 transition hover:bg-slate-50 lg:hidden"
                    :aria-expanded="menuOpen"
                    aria-controls="mobile-navigation"
                    :aria-label="
                        menuOpen ? 'Close navigation' : 'Open navigation'
                    "
                    @click="menuOpen = !menuOpen"
                >
                    <X v-if="menuOpen" class="size-5" aria-hidden="true" />
                    <Menu v-else class="size-5" aria-hidden="true" />
                </button>
            </div>
        </div>

        <nav
            v-if="menuOpen"
            id="mobile-navigation"
            class="border-t border-slate-200 bg-white px-5 py-4 lg:hidden"
            aria-label="Mobile navigation"
        >
            <div class="mx-auto grid max-w-7xl gap-1">
                <Link
                    v-for="item in navigation"
                    :key="item.path"
                    :href="item.href"
                    class="rounded-xl px-4 py-3 text-base font-semibold"
                    :class="
                        currentPath === item.path
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-700 hover:bg-slate-50'
                    "
                    @click="menuOpen = false"
                >
                    {{ item.label }}
                </Link>
                <Link
                    :href="contact()"
                    class="mt-2 inline-flex min-h-12 items-center justify-center rounded-xl bg-blue-600 px-5 font-bold text-white"
                    @click="menuOpen = false"
                >
                    Request a Free Demo
                </Link>
            </div>
        </nav>
    </header>
</template>
