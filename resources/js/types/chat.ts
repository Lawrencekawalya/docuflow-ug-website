export type ChatSender = 'visitor' | 'support';
export type ChatStatus = 'open' | 'closed';

export type ChatMessage = {
    id: number;
    sender_type: ChatSender;
    body: string;
    read_at: string | null;
    created_at: string;
};

export type ChatConversation = {
    id: number;
    reference: number;
    visitor_name: string;
    visitor_email: string;
    status: ChatStatus;
    last_message_at: string;
    messages: ChatMessage[];
};

export type ChatConversationSummary = Omit<ChatConversation, 'messages'> & {
    latest_message: string | null;
    unread_count: number;
};
