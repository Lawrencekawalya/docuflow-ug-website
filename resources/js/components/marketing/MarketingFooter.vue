<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Mail, MessageCircle, Phone } from 'lucide-vue-next';
import BrandMark from '@/components/marketing/BrandMark.vue';
import { about, contact, home, howItWorks, pricing } from '@/routes';
import type { DocuflowPublicConfig } from '@/types';

const config = usePage().props.docuflow as DocuflowPublicConfig;
const year = new Date().getFullYear();
const whatsappUrl = config.contact.whatsapp
    ? `https://wa.me/${config.contact.whatsapp.replace(/\D/g, '')}`
    : null;
</script>

<template>
    <footer class="bg-slate-950 text-slate-300">
        <div
            class="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:px-6 md:grid-cols-[1.4fr_1fr_1fr] lg:px-8"
        >
            <div class="max-w-md">
                <Link :href="home()"><BrandMark inverted /></Link>
                <p class="mt-5 text-sm leading-7 text-slate-400">
                    Practical document automation for Ugandan businesses. Turn invoices,
                    receipts and other business documents into structured, ready-to-use data.
                </p>
            </div>

            <div>
                <h2 class="text-sm font-bold tracking-wide text-white uppercase">Explore</h2>
                <nav class="mt-4 grid gap-3 text-sm" aria-label="Footer navigation">
                    <Link :href="howItWorks()" class="hover:text-white">How It Works</Link>
                    <Link :href="pricing()" class="hover:text-white">Pricing</Link>
                    <Link :href="about()" class="hover:text-white">About</Link>
                    <Link :href="contact()" class="hover:text-white">Request Demo</Link>
                </nav>
            </div>

            <div>
                <h2 class="text-sm font-bold tracking-wide text-white uppercase">Contact</h2>
                <div class="mt-4 grid gap-3 text-sm">
                    <a
                        v-if="config.contact.email"
                        :href="`mailto:${config.contact.email}`"
                        class="flex items-center gap-2 hover:text-white"
                    >
                        <Mail class="size-4" aria-hidden="true" />{{ config.contact.email }}
                    </a>
                    <a
                        v-if="config.contact.phone"
                        :href="`tel:${config.contact.phone}`"
                        class="flex items-center gap-2 hover:text-white"
                    >
                        <Phone class="size-4" aria-hidden="true" />{{ config.contact.phone }}
                    </a>
                    <a
                        v-if="whatsappUrl"
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 text-emerald-400 hover:text-emerald-300"
                    >
                        <MessageCircle class="size-4" aria-hidden="true" />WhatsApp DocuFlow
                    </a>
                    <Link v-if="!config.contact.email && !config.contact.phone" :href="contact()" class="hover:text-white">
                        Use our secure demo request form
                    </Link>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-800">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"
            >
                <p>© {{ year }} DocuFlow UG. All rights reserved.</p>
                <p>Reliable. Local. Practical. Secure.</p>
            </div>
        </div>
    </footer>
</template>
