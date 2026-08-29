<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CheckCircle2,
    Circle,
    LoaderCircle,
    Mail,
    MessageCircle,
    RotateCcw,
    Send,
    UserRound,
} from '@lucide/vue';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { JsonRequestError, jsonRequest } from '@/lib/jsonRequest';
import {
    index as conversationsIndex,
    messages as conversationMessages,
    show as showConversation,
    status as conversationStatus,
} from '@/routes/support/conversations';
import { store as storeSupportMessage } from '@/routes/support/conversations/messages';
import type {
    ChatConversation,
    ChatConversationSummary,
    ChatMessage,
} from '@/types';

const props = defineProps<{
    conversations: ChatConversationSummary[];
    selectedConversation: ChatConversation | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Support conversations',
                href: conversationsIndex(),
            },
        ],
    },
});

const activeConversation = ref<ChatConversation | null>(null);
const replyDraft = ref('');
const submitting = ref(false);
const statusUpdating = ref(false);
const error = ref('');
const messagesContainer = ref<HTMLElement | null>(null);
let pollTimer: ReturnType<typeof setInterval> | undefined;

const setActiveConversation = (conversation: ChatConversation | null): void => {
    activeConversation.value = conversation
        ? { ...conversation, messages: [...conversation.messages] }
        : null;
};

setActiveConversation(props.selectedConversation);

const formatTimestamp = (value: string): string =>
    new Intl.DateTimeFormat('en-UG', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        timeZone: 'Africa/Kampala',
    }).format(new Date(value));

const appendMessages = (incoming: ChatMessage[]): void => {
    if (!activeConversation.value) {
        return;
    }

    const knownIds = new Set(
        activeConversation.value.messages.map((message) => message.id),
    );
    activeConversation.value.messages.push(
        ...incoming.filter((message) => !knownIds.has(message.id)),
    );
};

const scrollToLatest = (): void => {
    requestAnimationFrame(() => {
        messagesContainer.value?.scrollTo({
            top: messagesContainer.value.scrollHeight,
            behavior: 'smooth',
        });
    });
};

const pollMessages = async (): Promise<void> => {
    if (!activeConversation.value) {
        return;
    }

    const conversationId = activeConversation.value.id;
    const lastMessage = activeConversation.value.messages.at(-1);

    try {
        const response = await jsonRequest<{
            status: ChatConversation['status'];
            messages: ChatMessage[];
        }>(
            conversationMessages.url(conversationId, {
                query: { after: lastMessage?.id ?? 0 },
            }),
        );

        if (
            !activeConversation.value?.id ||
            activeConversation.value.id !== conversationId
        ) {
            return;
        }

        activeConversation.value.status = response.status;

        if (response.messages.length > 0) {
            appendMessages(response.messages);
            scrollToLatest();
        }
    } catch {
        // Polling retries automatically; manual sends still surface errors.
    }
};

const sendReply = async (): Promise<void> => {
    const body = replyDraft.value.trim();

    if (!body || !activeConversation.value || submitting.value) {
        return;
    }

    submitting.value = true;
    error.value = '';

    try {
        const response = await jsonRequest<{ message: ChatMessage }>(
            storeSupportMessage.url(activeConversation.value.id),
            {
                method: 'POST',
                body: JSON.stringify({ message: body }),
            },
        );
        appendMessages([response.message]);
        replyDraft.value = '';
        scrollToLatest();
    } catch (requestError) {
        error.value =
            requestError instanceof JsonRequestError
                ? requestError.message
                : 'Your reply could not be sent. Please try again.';
    } finally {
        submitting.value = false;
    }
};

const setConversationStatus = async (
    status: ChatConversation['status'],
): Promise<void> => {
    if (!activeConversation.value || statusUpdating.value) {
        return;
    }

    statusUpdating.value = true;
    error.value = '';

    try {
        const response = await jsonRequest<{
            status: ChatConversation['status'];
        }>(conversationStatus.url(activeConversation.value.id), {
            method: 'PATCH',
            body: JSON.stringify({ status }),
        });
        activeConversation.value.status = response.status;
    } catch (requestError) {
        error.value =
            requestError instanceof JsonRequestError
                ? requestError.message
                : 'The conversation status could not be updated.';
    } finally {
        statusUpdating.value = false;
    }
};

watch(
    () => props.selectedConversation,
    (conversation) => {
        setActiveConversation(conversation);
        replyDraft.value = '';
        error.value = '';
        scrollToLatest();
    },
);

onMounted(() => {
    scrollToLatest();
    pollTimer = setInterval(() => void pollMessages(), 4000);
});

onUnmounted(() => {
    if (pollTimer) {
        clearInterval(pollTimer);
    }
});
</script>

<template>
    <Head title="Support Conversations" />

    <div class="flex min-h-0 flex-1 flex-col p-4 md:p-6">
        <div class="mb-5">
            <p
                class="text-sm font-bold tracking-widest text-blue-600 uppercase"
            >
                Support inbox
            </p>
            <h1 class="mt-1 text-3xl font-extrabold tracking-tight">
                Website conversations
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                Reply to visitors from your phone or computer. New messages
                appear automatically while this screen is open.
            </p>
        </div>

        <div
            class="grid min-h-[calc(100dvh-12rem)] overflow-hidden rounded-2xl border bg-background shadow-sm lg:grid-cols-[360px_minmax(0,1fr)]"
        >
            <aside
                class="min-h-0 flex-col border-r"
                :class="activeConversation ? 'hidden lg:flex' : 'flex'"
            >
                <div
                    class="flex h-24 shrink-0 flex-col justify-center border-b px-5"
                >
                    <h2 class="font-extrabold">Conversations</h2>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ conversations.length }} most recent
                    </p>
                </div>
                <div
                    v-if="conversations.length"
                    class="min-h-0 overflow-y-auto"
                >
                    <Link
                        v-for="conversation in conversations"
                        :key="conversation.id"
                        :href="showConversation(conversation.id)"
                        class="flex gap-3 border-b px-4 py-4 transition hover:bg-muted/60"
                        :class="
                            activeConversation?.id === conversation.id
                                ? 'bg-blue-50 dark:bg-blue-950/30'
                                : ''
                        "
                    >
                        <span
                            class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                        >
                            <UserRound class="size-5" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span
                                class="flex items-center justify-between gap-2"
                            >
                                <strong class="truncate text-sm">{{
                                    conversation.visitor_name
                                }}</strong>
                                <span
                                    class="shrink-0 text-[10px] text-muted-foreground"
                                >
                                    {{
                                        formatTimestamp(
                                            conversation.last_message_at,
                                        )
                                    }}
                                </span>
                            </span>
                            <span class="mt-1 flex items-center gap-2">
                                <span
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ conversation.latest_message }}
                                </span>
                                <span
                                    v-if="conversation.unread_count > 0"
                                    class="ml-auto grid size-5 shrink-0 place-items-center rounded-full bg-blue-600 text-[10px] font-bold text-white"
                                    >{{ conversation.unread_count }}</span
                                >
                            </span>
                            <span
                                class="mt-2 flex items-center gap-1.5 text-[10px] font-bold uppercase"
                            >
                                <Circle
                                    class="size-2.5 fill-current"
                                    :class="
                                        conversation.status === 'open'
                                            ? 'text-emerald-500'
                                            : 'text-slate-400'
                                    "
                                />
                                #{{ conversation.reference }} ·
                                {{ conversation.status }}
                            </span>
                        </span>
                    </Link>
                </div>
                <div
                    v-else
                    class="grid flex-1 place-items-center p-8 text-center"
                >
                    <div>
                        <MessageCircle
                            class="mx-auto size-10 text-muted-foreground/50"
                        />
                        <p class="mt-3 font-bold">No conversations yet</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            New website chats will appear here.
                        </p>
                    </div>
                </div>
            </aside>

            <section
                v-if="activeConversation"
                class="flex min-h-0 flex-col bg-slate-50 dark:bg-slate-950"
            >
                <header
                    class="flex h-24 shrink-0 items-center gap-3 border-b bg-background px-4 sm:px-5"
                >
                    <Link
                        :href="conversationsIndex()"
                        class="grid size-10 shrink-0 place-items-center rounded-xl hover:bg-muted lg:hidden"
                        aria-label="Back to conversations"
                    >
                        <ArrowLeft class="size-5" />
                    </Link>
                    <span
                        class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                    >
                        <UserRound class="size-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="truncate font-extrabold">
                            {{ activeConversation.visitor_name }}
                        </h2>
                        <a
                            :href="`mailto:${activeConversation.visitor_email}`"
                            class="flex items-center gap-1 truncate text-xs text-muted-foreground hover:text-blue-600"
                        >
                            <Mail class="size-3" />
                            {{ activeConversation.visitor_email }}
                        </a>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-muted-foreground">
                            #{{ activeConversation.reference }}
                        </p>
                        <button
                            v-if="activeConversation.status === 'open'"
                            type="button"
                            :disabled="statusUpdating"
                            class="mt-1 inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900 disabled:opacity-50 dark:text-emerald-400"
                            @click="setConversationStatus('closed')"
                        >
                            <CheckCircle2 class="size-3.5" /> Close
                        </button>
                        <button
                            v-else
                            type="button"
                            :disabled="statusUpdating"
                            class="mt-1 inline-flex items-center gap-1 text-xs font-bold text-blue-700 hover:text-blue-900 disabled:opacity-50 dark:text-blue-400"
                            @click="setConversationStatus('open')"
                        >
                            <RotateCcw class="size-3.5" /> Reopen
                        </button>
                    </div>
                </header>

                <div
                    ref="messagesContainer"
                    class="min-h-0 flex-1 space-y-4 overflow-y-auto p-4 sm:p-6"
                    aria-live="polite"
                >
                    <div class="flex justify-center">
                        <span
                            class="rounded-full border bg-background px-3 py-1 text-xs text-muted-foreground"
                            >Started
                            {{
                                formatTimestamp(
                                    activeConversation.messages[0]
                                        ?.created_at ??
                                        activeConversation.last_message_at,
                                )
                            }}</span
                        >
                    </div>
                    <div
                        v-for="message in activeConversation.messages"
                        :key="message.id"
                        class="flex"
                        :class="
                            message.sender_type === 'support'
                                ? 'justify-end'
                                : 'justify-start'
                        "
                    >
                        <div
                            class="max-w-[82%] rounded-2xl px-4 py-3 shadow-sm"
                            :class="
                                message.sender_type === 'support'
                                    ? 'rounded-br-md bg-blue-600 text-white'
                                    : 'rounded-bl-md border bg-background'
                            "
                        >
                            <p class="text-sm leading-6 whitespace-pre-wrap">
                                {{ message.body }}
                            </p>
                            <p
                                class="mt-1 text-right text-[10px]"
                                :class="
                                    message.sender_type === 'support'
                                        ? 'text-blue-100'
                                        : 'text-muted-foreground'
                                "
                            >
                                {{ formatTimestamp(message.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <footer class="border-t bg-background p-4 sm:p-5">
                    <p
                        v-if="error"
                        role="alert"
                        class="mb-2 text-sm text-destructive"
                    >
                        {{ error }}
                    </p>
                    <form
                        v-if="activeConversation.status === 'open'"
                        class="flex items-end gap-3"
                        @submit.prevent="sendReply"
                    >
                        <label class="min-w-0 flex-1">
                            <span class="sr-only">Reply</span>
                            <textarea
                                v-model="replyDraft"
                                rows="2"
                                maxlength="2000"
                                class="max-h-36 min-h-14 w-full resize-none rounded-xl border bg-background px-4 py-3 text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-950"
                                placeholder="Write a reply…"
                                @keydown.enter.exact.prevent="sendReply"
                            />
                        </label>
                        <button
                            type="submit"
                            :disabled="submitting || !replyDraft.trim()"
                            class="grid size-12 shrink-0 place-items-center rounded-xl bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                            aria-label="Send reply"
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
                        class="flex items-center justify-between gap-4 rounded-xl bg-muted p-3 text-sm"
                    >
                        <span>This conversation is closed.</span>
                        <button
                            type="button"
                            class="font-bold text-blue-700 dark:text-blue-400"
                            @click="setConversationStatus('open')"
                        >
                            Reopen to reply
                        </button>
                    </div>
                </footer>
            </section>

            <section
                v-else
                class="hidden place-items-center bg-muted/20 p-10 text-center lg:grid"
            >
                <div>
                    <MessageCircle
                        class="mx-auto size-14 text-muted-foreground/40"
                    />
                    <h2 class="mt-5 text-xl font-extrabold">
                        Select a conversation
                    </h2>
                    <p class="mt-2 max-w-sm text-sm text-muted-foreground">
                        Choose a visitor from the inbox to read their messages
                        and reply.
                    </p>
                </div>
            </section>
        </div>
    </div>
</template>
