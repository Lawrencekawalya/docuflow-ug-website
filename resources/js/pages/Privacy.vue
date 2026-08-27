<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DocumentHeroPattern from '@/components/marketing/DocumentHeroPattern.vue';
import { contact } from '@/routes';
</script>

<template>
    <Head title="Privacy Notice">
        <meta
            name="description"
            content="How DocuFlow UG handles information submitted through its demo request form."
        />
    </Head>
    <section
        class="relative overflow-hidden bg-slate-950 px-5 py-16 text-white sm:px-6 lg:px-8"
    >
        <DocumentHeroPattern />
        <div class="relative z-10 mx-auto max-w-4xl">
            <p
                class="text-sm font-extrabold tracking-[0.14em] text-blue-400 uppercase"
            >
                Privacy
            </p>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight sm:text-5xl">
                Demo request privacy notice
            </h1>
            <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-300">
                A plain-language explanation of how information submitted
                through this website is used.
            </p>
        </div>
    </section>
    <section class="px-5 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="mx-auto max-w-4xl space-y-10 text-slate-600">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-950">
                    Information we collect
                </h2>
                <p class="mt-3 leading-8">
                    When you request a demonstration, we collect the contact and
                    business-workflow information you choose to provide. The
                    public form is not intended for confidential documents,
                    passwords or access credentials.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-950">
                    How we use it
                </h2>
                <p class="mt-3 leading-8">
                    We use the information to understand your request, assess
                    whether DocuFlow may fit your workflow, contact you using
                    your preferred method and arrange an appropriate next step.
                    We do not present submitted details as public testimonials.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-950">
                    Storage and access
                </h2>
                <p class="mt-3 leading-8">
                    Requests are stored as business leads and may be delivered
                    to the configured DocuFlow email or workflow. Access should
                    be limited to people responsible for reviewing and
                    responding to enquiries. Records should be retained only
                    while needed for that purpose and legitimate business
                    administration.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-950">
                    Your choices
                </h2>
                <p class="mt-3 leading-8">
                    You may ask DocuFlow UG to correct or remove the personal
                    information associated with your enquiry. Use the contact
                    page and identify the relevant request so the team can
                    respond.
                </p>
            </div>
            <div class="rounded-2xl bg-blue-50 p-6">
                <h2 class="text-xl font-extrabold text-slate-950">
                    Questions about your information?
                </h2>
                <p class="mt-2 leading-7">
                    Contact DocuFlow UG through the same secure enquiry channel.
                </p>
                <Link
                    :href="contact()"
                    class="mt-4 inline-flex min-h-11 items-center rounded-xl bg-blue-600 px-5 font-bold text-white"
                    >Go to Contact</Link
                >
            </div>
        </div>
    </section>
</template>
