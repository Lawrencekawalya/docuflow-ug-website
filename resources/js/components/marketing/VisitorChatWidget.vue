<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Check,
    CheckCheck,
    CheckCircle2,
    LoaderCircle,
    MessageCircle,
    Send,
    X,
} from '@lucide/vue';
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { JsonRequestError, jsonRequest } from '@/lib/jsonRequest';
import { privacy } from '@/routes';
import {
    messages as chatMessages,
    show as showChat,
    store as startChat,
} from '@/routes/chat';
import { store as storeChatMessage } from '@/routes/chat/messages';
import type { ChatConversation, ChatMessage } from '@/types';

type ConversationResponse = { conversation: ChatConversation | null };
type MessagesResponse = {
    status: ChatConversation['status'];
    messages: ChatMessage[];
    visitor_read_receipt: {
        through_id: number;
        read_at: string;
    } | null;
};

const isOpen = ref(false);
const loading = ref(true);
const submitting = ref(false);
const conversation = ref<ChatConversation | null>(null);
const visitorName = ref('');
const visitorEmail = ref('');
const openingMessage = ref('');
const messageDraft = ref('');
const website = ref('');
const error = ref('');
const fieldErrors = ref<Record<string, string[]>>({});
const messagesContainer = ref<HTMLElement | null>(null);
let pollTimer: ReturnType<typeof setInterval> | undefined;

const formatTime = (value: string): string =>
    new Intl.DateTimeFormat('en-UG', {
        hour: 'numeric',
        minute: '2-digit',
        timeZone: 'Africa/Kampala',
    }).format(new Date(value));

const scrollToLatest = async (): Promise<void> => {
    await nextTick();
    messagesContainer.value?.scrollTo({
        top: messagesContainer.value.scrollHeight,
        behavior: 'smooth',
    });
};

const appendMessages = (incoming: ChatMessage[]): void => {
    if (!conversation.value) {
        return;
    }

    const knownIds = new Set(
        conversation.value.messages.map((message) => message.id),
    );
    conversation.value.messages.push(
        ...incoming.filter((message) => !knownIds.has(message.id)),
    );
};

const applyReadReceipt = (
    receipt: MessagesResponse['visitor_read_receipt'],
): void => {
    if (!conversation.value || receipt === null) {
        return;
    }

    conversation.value.messages = conversation.value.messages.map((message) =>
        message.sender_type === 'visitor' && message.id <= receipt.through_id
            ? {
                  ...message,
                  read_at: message.read_at ?? receipt.read_at,
              }
            : message,
    );
};

const restoreConversation = async (): Promise<void> => {
    try {
        const response = await jsonRequest<ConversationResponse>(
            showChat.url(),
        );
        conversation.value = response.conversation;
    } catch {
        error.value = 'Chat could not be loaded. Please refresh the page.';
    } finally {
        loading.value = false;
    }
};

const startConversation = async (): Promise<void> => {
    submitting.value = true;
    error.value = '';
    fieldErrors.value = {};

    try {
        const response = await jsonRequest<ConversationResponse>(
            startChat.url(),
            {
                method: 'POST',
                body: JSON.stringify({
                    visitor_name: visitorName.value,
                    visitor_email: visitorEmail.value,
                    message: openingMessage.value,
                    website: website.value,
                }),
            },
        );
        conversation.value = response.conversation;
        openingMessage.value = '';
        await scrollToLatest();
    } catch (requestError) {
        if (requestError instanceof JsonRequestError) {
            error.value = requestError.message;
            fieldErrors.value = requestError.errors;
        } else {
            error.value = 'Your message could not be sent. Please try again.';
        }
    } finally {
        submitting.value = false;
    }
};

const sendMessage = async (): Promise<void> => {
    const body = messageDraft.value.trim();

    if (!body || !conversation.value || submitting.value) {
        return;
    }

    submitting.value = true;
    error.value = '';

    try {
        const response = await jsonRequest<{ message: ChatMessage }>(
            storeChatMessage.url(),
            {
                method: 'POST',
                body: JSON.stringify({ message: body, website: website.value }),
            },
        );
        appendMessages([response.message]);
        messageDraft.value = '';
        await scrollToLatest();
    } catch (requestError) {
        error.value =
            requestError instanceof JsonRequestError
                ? requestError.message
                : 'Your message could not be sent. Please try again.';
    } finally {
        submitting.value = false;
    }
};

const pollMessages = async (): Promise<void> => {
    if (!conversation.value || !isOpen.value) {
        return;
    }

    const lastMessage = conversation.value.messages.at(-1);

    try {
        const response = await jsonRequest<MessagesResponse>(
            chatMessages.url({
                query: { after: lastMessage?.id ?? 0 },
            }),
        );
        conversation.value.status = response.status;
        applyReadReceipt(response.visitor_read_receipt);

        if (response.messages.length > 0) {
            appendMessages(response.messages);
            await scrollToLatest();
        }
    } catch {
        // The next polling interval will retry without interrupting the visitor.
    }
};

const restartPolling = (): void => {
    if (pollTimer) {
        clearInterval(pollTimer);
    }

    pollTimer = undefined;

    if (isOpen.value && conversation.value) {
        void pollMessages();
        pollTimer = setInterval(() => void pollMessages(), 5000);
    }
};

watch(
    () => [isOpen.value, conversation.value?.id],
    () => {
        restartPolling();

        if (isOpen.value) {
            void scrollToLatest();
        }
    },
);

onMounted(() => void restoreConversation());
onUnmounted(() => {
    if (pollTimer) {
        clearInterval(pollTimer);
    }
});
</script>

<template>
    <aside
        v-if="isOpen"
        aria-label="DocuFlow support chat"
        class="fixed right-3 bottom-3 z-[70] flex max-h-[min(680px,calc(100dvh-1.5rem))] w-[calc(100%-1.5rem)] max-w-[390px] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-950/25 sm:right-6 sm:bottom-6"
    >
        <header
            class="flex items-center gap-3 bg-slate-950 px-5 py-4 text-white"
        >
            <span
                class="grid size-10 shrink-0 place-items-center rounded-xl bg-blue-600"
            >
                <MessageCircle class="size-5" aria-hidden="true" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-extrabold">Chat with DocuFlow</p>
                <p class="flex items-center gap-1.5 text-xs text-slate-300">
                    <span class="size-2 rounded-full bg-emerald-400" />
                    We usually reply within 48 hours
                </p>
            </div>
            <button
                type="button"
                class="grid size-10 place-items-center rounded-xl text-slate-300 transition hover:bg-white/10 hover:text-white"
                aria-label="Minimize chat"
                @click="isOpen = false"
            >
                <X class="size-5" />
            </button>
        </header>

        <div v-if="loading" class="grid min-h-64 place-items-center">
            <LoaderCircle class="size-7 animate-spin text-blue-600" />
        </div>

        <form
            v-else-if="!conversation"
            class="grid gap-4 overflow-y-auto p-5"
            @submit.prevent="startConversation"
        >
            <div>
                <h2 class="text-xl font-extrabold text-slate-950">
                    How can we help?
                </h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Leave a message and return to this browser to continue the
                    conversation.
                </p>
            </div>
            <label class="text-sm font-bold text-slate-800">
                Your name
                <input
                    v-model="visitorName"
                    required
                    maxlength="120"
                    autocomplete="name"
                    class="mt-1.5 min-h-11 w-full rounded-xl border border-slate-300 px-3.5 text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <span
                    v-if="fieldErrors.visitor_name?.[0]"
                    class="mt-1 block text-xs font-medium text-red-600"
                    >{{ fieldErrors.visitor_name[0] }}</span
                >
            </label>
            <label class="text-sm font-bold text-slate-800">
                Email address
                <input
                    v-model="visitorEmail"
                    required
                    type="email"
                    maxlength="190"
                    autocomplete="email"
                    class="mt-1.5 min-h-11 w-full rounded-xl border border-slate-300 px-3.5 text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
                <span
                    v-if="fieldErrors.visitor_email?.[0]"
                    class="mt-1 block text-xs font-medium text-red-600"
                    >{{ fieldErrors.visitor_email[0] }}</span
                >
            </label>
            <label class="text-sm font-bold text-slate-800">
                Message
                <textarea
                    v-model="openingMessage"
                    required
                    maxlength="2000"
                    rows="4"
                    class="mt-1.5 w-full resize-none rounded-xl border border-slate-300 p-3.5 text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    placeholder="Tell us what you would like to automate…"
                />
                <span
                    v-if="fieldErrors.message?.[0]"
                    class="mt-1 block text-xs font-medium text-red-600"
                    >{{ fieldErrors.message[0] }}</span
                >
            </label>
            <label class="sr-only" aria-hidden="true">
                Website
                <input v-model="website" tabindex="-1" autocomplete="off" />
            </label>
            <p v-if="error" role="alert" class="text-sm text-red-600">
                {{ error }}
            </p>
            <button
                type="submit"
                :disabled="submitting"
                class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <LoaderCircle v-if="submitting" class="size-4 animate-spin" />
                <Send v-else class="size-4" />
                Send message
            </button>
            <p class="text-center text-xs leading-5 text-slate-500">
                Don’t share passwords or confidential documents. See our
                <Link :href="privacy()" class="font-bold text-blue-700"
                    >privacy notice</Link
                >.
            </p>
        </form>

        <template v-else>
            <div
                class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-2.5 text-xs"
            >
                <span class="font-bold text-slate-700"
                    >Request #{{ conversation.reference }}</span
                >
                <span
                    class="rounded-full px-2.5 py-1 font-bold capitalize"
                    :class="
                        conversation.status === 'open'
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'bg-slate-200 text-slate-700'
                    "
                    >{{ conversation.status }}</span
                >
            </div>
            <div
                ref="messagesContainer"
                class="min-h-64 flex-1 space-y-3 overflow-y-auto bg-slate-50 p-4 sm:min-h-80"
                aria-live="polite"
            >
                <div class="flex justify-center py-1">
                    <span
                        class="rounded-full bg-white px-3 py-1 text-xs text-slate-500 shadow-sm"
                        >Conversation with DocuFlow support</span
                    >
                </div>
                <div
                    v-for="message in conversation.messages"
                    :key="message.id"
                    class="flex"
                    :class="
                        message.sender_type === 'visitor'
                            ? 'justify-end'
                            : 'justify-start'
                    "
                >
                    <div
                        class="max-w-[85%] rounded-2xl px-4 py-2.5 shadow-sm"
                        :class="
                            message.sender_type === 'visitor'
                                ? 'rounded-br-md bg-blue-600 text-white'
                                : 'rounded-bl-md border border-slate-200 bg-white text-slate-800'
                        "
                    >
                        <p class="text-sm leading-6 whitespace-pre-wrap">
                            {{ message.body }}
                        </p>
                        <p
                            class="mt-1 flex items-center justify-end gap-1 text-[10px]"
                            :class="
                                message.sender_type === 'visitor'
                                    ? 'text-blue-100'
                                    : 'text-slate-400'
                            "
                        >
                            <span>{{ formatTime(message.created_at) }}</span>
                            <template v-if="message.sender_type === 'visitor'">
                                <CheckCheck
                                    v-if="message.read_at"
                                    class="size-3.5 text-cyan-200"
                                    aria-hidden="true"
                                />
                                <Check
                                    v-else
                                    class="size-3.5"
                                    aria-hidden="true"
                                />
                                <span>{{
                                    message.read_at ? 'Read' : 'Delivered'
                                }}</span>
                            </template>
                        </p>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-200 bg-white p-4">
                <p v-if="error" role="alert" class="mb-2 text-xs text-red-600">
                    {{ error }}
                </p>
                <form
                    v-if="conversation.status === 'open'"
                    class="flex items-end gap-2"
                    @submit.prevent="sendMessage"
                >
                    <label class="min-w-0 flex-1">
                        <span class="sr-only">Message</span>
                        <textarea
                            v-model="messageDraft"
                            rows="1"
                            maxlength="2000"
                            class="max-h-28 min-h-11 w-full resize-none rounded-xl border border-slate-300 px-3.5 py-2.5 text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            placeholder="Type your message…"
                            @keydown.enter.exact.prevent="sendMessage"
                        />
                    </label>
                    <button
                        type="submit"
                        :disabled="submitting || !messageDraft.trim()"
                        class="grid size-11 shrink-0 place-items-center rounded-xl bg-blue-600 text-white transition hover:bg-blue-700 disabled:opacity-50"
                        aria-label="Send message"
                    >
                        <LoaderCircle
                            v-if="submitting"
                            class="size-5 animate-spin"
                        />
                        <Send v-else class="size-5" />
                    </button>
                </form>
                <div
                    v-else
                    class="flex items-start gap-2 rounded-xl bg-slate-100 p-3 text-sm text-slate-600"
                >
                    <CheckCircle2 class="mt-0.5 size-4 shrink-0" />
                    This conversation has been closed by support.
                </div>
            </div>
        </template>
    </aside>

    <button
        v-else
        type="button"
        class="fixed right-4 bottom-4 z-[70] inline-flex min-h-14 items-center gap-2 rounded-full bg-blue-600 px-5 font-extrabold text-white shadow-xl shadow-blue-950/25 transition hover:-translate-y-0.5 hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:right-6 sm:bottom-6"
        aria-label="Open support chat"
        @click="isOpen = true"
    >
        <MessageCircle class="size-5" />
        <span>Chat with us</span>
    </button>
</template>
