<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Clock3,
    Mail,
    MessageCircle,
    Phone,
    Send,
} from '@lucide/vue';
import { store } from '@/actions/App/Http/Controllers/DemoRequestController';
import InputError from '@/components/InputError.vue';
import { privacy } from '@/routes';
import type { DocuflowPublicConfig } from '@/types';

const config = (usePage().props.docuflow as DocuflowPublicConfig).contact;
const whatsappUrl = config.whatsapp
    ? `https://wa.me/${config.whatsapp.replace(/\D/g, '')}`
    : null;
const documentTypes = [
    ['invoices', 'Invoices'],
    ['receipts', 'Receipts'],
    ['purchase-orders', 'Purchase orders'],
    ['statements', 'Statements'],
    ['delivery-notes', 'Delivery notes'],
    ['other', 'Other documents'],
];
const inputClass =
    'mt-2 min-h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-base text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100';
</script>

<template>
    <Head title="Request a Demo"
        ><meta
            name="description"
            content="Tell DocuFlow UG how your team handles business documents and request a practical, workflow-focused demonstration."
    /></Head>
    <section
        class="bg-slate-950 px-5 py-20 text-white sm:px-6 lg:px-8 lg:py-24"
    >
        <div class="mx-auto max-w-4xl text-center">
            <p
                class="text-sm font-extrabold tracking-[0.14em] text-blue-400 uppercase"
            >
                Request a demo
            </p>
            <h1
                class="mt-4 text-4xl font-extrabold tracking-[-0.045em] text-balance sm:text-5xl lg:text-6xl"
            >
                Let's find out what you can automate
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-lg leading-8 text-slate-300">
                Tell us how your team currently handles documents. We'll review
                the process and show you where DocuFlow could save time and
                reduce repetitive work.
            </p>
        </div>
    </section>

    <section class="bg-slate-50 px-5 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1fr_1.8fr]">
            <aside class="order-2 lg:sticky lg:top-24 lg:order-1 lg:self-start">
                <p
                    class="text-sm font-extrabold tracking-[0.14em] text-blue-600 uppercase"
                >
                    What happens next
                </p>
                <h2
                    class="mt-3 text-3xl font-extrabold tracking-tight text-slate-950"
                >
                    A useful conversation, not a generic sales call
                </h2>
                <ol class="mt-8 grid gap-5">
                    <li class="flex gap-4">
                        <span
                            class="grid size-9 shrink-0 place-items-center rounded-full bg-blue-600 font-bold text-white"
                            >1</span
                        >
                        <div>
                            <h3 class="font-extrabold text-slate-900">
                                We review your workflow
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Your answers help us understand the documents,
                                volume and main bottleneck.
                            </p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span
                            class="grid size-9 shrink-0 place-items-center rounded-full bg-blue-600 font-bold text-white"
                            >2</span
                        >
                        <div>
                            <h3 class="font-extrabold text-slate-900">
                                We arrange the next step
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                We contact you using your preferred channel to
                                clarify the process.
                            </p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span
                            class="grid size-9 shrink-0 place-items-center rounded-full bg-blue-600 font-bold text-white"
                            >3</span
                        >
                        <div>
                            <h3 class="font-extrabold text-slate-900">
                                You see a realistic demonstration
                            </h3>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Evaluate practical fit before deciding whether
                                to proceed.
                            </p>
                        </div>
                    </li>
                </ol>
                <div class="mt-10 grid gap-3">
                    <a
                        v-if="config.email"
                        :href="`mailto:${config.email}`"
                        class="flex min-h-12 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 font-bold text-slate-800"
                        ><Mail class="size-5 text-blue-600" />{{
                            config.email
                        }}</a
                    ><a
                        v-if="config.phone"
                        :href="`tel:${config.phone}`"
                        class="flex min-h-12 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 font-bold text-slate-800"
                        ><Phone class="size-5 text-blue-600" />{{
                            config.phone
                        }}</a
                    ><a
                        v-if="whatsappUrl"
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex min-h-12 items-center gap-3 rounded-xl bg-emerald-600 px-4 font-bold text-white"
                        ><MessageCircle class="size-5" />Chat on WhatsApp</a
                    >
                    <div
                        v-if="
                            !config.email && !config.phone && !config.whatsapp
                        "
                        class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-950"
                    >
                        <strong>Pre-launch gate:</strong> add the approved
                        professional email, telephone and WhatsApp details to
                        the environment configuration.
                    </div>
                </div>
            </aside>

            <div
                class="order-1 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 lg:order-2 lg:p-10"
            >
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-slate-950">
                        Tell us about your document workflow
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Fields marked with
                        <span class="text-red-600">*</span> are required.
                    </p>
                </div>
                <Form
                    v-bind="store.form()"
                    reset-on-success
                    v-slot="{ errors, processing, recentlySuccessful }"
                    class="grid gap-6"
                >
                    <div
                        v-if="recentlySuccessful"
                        role="status"
                        class="flex gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-950"
                    >
                        <CheckCircle2
                            class="mt-0.5 size-5 shrink-0 text-emerald-600"
                        />
                        <p class="text-sm leading-6">
                            <strong
                                >Thanks — your demo request has been
                                received.</strong
                            ><br />We'll review your workflow information and
                            contact you to arrange the next step.
                        </p>
                    </div>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <label class="block text-sm font-bold text-slate-800"
                            >Full Name <span class="text-red-600">*</span
                            ><input
                                name="full_name"
                                autocomplete="name"
                                required
                                :class="inputClass"
                                placeholder="Your full name" /><InputError
                                class="mt-2"
                                :message="errors.full_name"
                        /></label>
                        <label class="block text-sm font-bold text-slate-800"
                            >Business Name <span class="text-red-600">*</span
                            ><input
                                name="business_name"
                                autocomplete="organization"
                                required
                                :class="inputClass"
                                placeholder="Your business" /><InputError
                                class="mt-2"
                                :message="errors.business_name"
                        /></label>
                        <label class="block text-sm font-bold text-slate-800"
                            >Work Email <span class="text-red-600">*</span
                            ><input
                                name="work_email"
                                type="email"
                                autocomplete="email"
                                required
                                :class="inputClass"
                                placeholder="you@business.com" /><InputError
                                class="mt-2"
                                :message="errors.work_email"
                        /></label>
                        <label class="block text-sm font-bold text-slate-800"
                            >Phone / WhatsApp<input
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                :class="inputClass"
                                placeholder="+256 …" /><InputError
                                class="mt-2"
                                :message="errors.phone"
                        /></label>
                        <label class="block text-sm font-bold text-slate-800"
                            >Location<input
                                name="location"
                                autocomplete="address-level2"
                                :class="inputClass"
                                placeholder="Town or district" /><InputError
                                class="mt-2"
                                :message="errors.location"
                        /></label>
                        <label class="block text-sm font-bold text-slate-800"
                            >Documents per month<select
                                name="monthly_document_volume"
                                :class="inputClass"
                            >
                                <option value="">Choose an estimate</option>
                                <option value="under-100">
                                    Fewer than 100
                                </option>
                                <option value="100-500">100–500</option>
                                <option value="501-1000">501–1,000</option>
                                <option value="1001-3000">1,001–3,000</option>
                                <option value="3000-plus">
                                    More than 3,000
                                </option>
                                <option value="not-sure">
                                    Not sure yet
                                </option></select
                            ><InputError
                                class="mt-2"
                                :message="errors.monthly_document_volume"
                        /></label>
                    </div>

                    <fieldset>
                        <legend class="text-sm font-bold text-slate-800">
                            What type of documents do you process?
                            <span class="text-red-600">*</span>
                        </legend>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <label
                                v-for="[value, label] in documentTypes"
                                :key="value"
                                class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50"
                                ><input
                                    type="checkbox"
                                    name="document_types[]"
                                    :value="value"
                                    class="size-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                />{{ label }}</label
                            >
                        </div>
                        <InputError
                            class="mt-2"
                            :message="errors.document_types"
                        />
                    </fieldset>

                    <label class="block text-sm font-bold text-slate-800"
                        >How do you currently process them?<textarea
                            name="current_process"
                            rows="4"
                            :class="[inputClass, 'py-3']"
                            placeholder="Describe the current steps, tools or handoffs…" /><InputError
                            class="mt-2"
                            :message="errors.current_process"
                    /></label>
                    <label class="block text-sm font-bold text-slate-800"
                        >What's your biggest challenge?<textarea
                            name="biggest_challenge"
                            rows="4"
                            :class="[inputClass, 'py-3']"
                            placeholder="For example: typing time, errors, delays or tracking…" /><InputError
                            class="mt-2"
                            :message="errors.biggest_challenge"
                    /></label>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <label class="block text-sm font-bold text-slate-800"
                            >Preferred contact method<select
                                name="preferred_contact_method"
                                :class="inputClass"
                            >
                                <option value="">No preference</option>
                                <option value="email">Email</option>
                                <option value="phone">Phone call</option>
                                <option value="whatsapp">
                                    WhatsApp
                                </option></select
                            ><InputError
                                class="mt-2"
                                :message="
                                    errors.preferred_contact_method
                                " /></label
                        ><label class="block text-sm font-bold text-slate-800"
                            >Additional details<textarea
                                name="message"
                                rows="3"
                                :class="[inputClass, 'py-3']"
                                placeholder="Anything else we should know?" /><InputError
                                class="mt-2"
                                :message="errors.message"
                        /></label>
                    </div>

                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label
                            >Website<input
                                name="website"
                                tabindex="-1"
                                autocomplete="off"
                        /></label>
                    </div>
                    <div class="border-t border-slate-100 pt-6">
                        <p class="text-xs leading-5 text-slate-500">
                            By requesting a demo, you agree that DocuFlow UG may
                            use these details to review your workflow and
                            contact you about this request. Read our
                            <Link
                                :href="privacy()"
                                class="font-bold text-blue-700 underline"
                                >privacy notice</Link
                            >. Do not submit confidential documents or
                            credentials through this form.
                        </p>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="mt-5 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 font-extrabold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                        >
                            <Clock3
                                v-if="processing"
                                class="size-4 animate-spin"
                            /><Send v-else class="size-4" />{{
                                processing
                                    ? 'Sending request…'
                                    : 'Request My Demo'
                            }}
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </section>
</template>
