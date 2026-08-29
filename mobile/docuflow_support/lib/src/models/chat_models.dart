class SupportUser {
  const SupportUser({
    required this.id,
    required this.name,
    required this.email,
  });

  factory SupportUser.fromJson(Map<String, dynamic> json) => SupportUser(
    id: json['id'] as int,
    name: json['name'] as String,
    email: json['email'] as String,
  );

  final int id;
  final String name;
  final String email;
}

class ChatMessage {
  const ChatMessage({
    required this.id,
    required this.senderType,
    required this.body,
    required this.createdAt,
    this.readAt,
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) => ChatMessage(
    id: json['id'] as int,
    senderType: json['sender_type'] as String,
    body: json['body'] as String,
    createdAt: DateTime.parse(json['created_at'] as String),
    readAt: json['read_at'] == null
        ? null
        : DateTime.parse(json['read_at'] as String),
  );

  final int id;
  final String senderType;
  final String body;
  final DateTime createdAt;
  final DateTime? readAt;

  bool get isSupport => senderType == 'support';
}

class ConversationSummary {
  const ConversationSummary({
    required this.id,
    required this.reference,
    required this.visitorName,
    required this.visitorEmail,
    required this.status,
    required this.lastMessageAt,
    required this.latestMessage,
    required this.unreadCount,
  });

  factory ConversationSummary.fromJson(Map<String, dynamic> json) =>
      ConversationSummary(
        id: json['id'] as int,
        reference: json['reference'] as int,
        visitorName: json['visitor_name'] as String,
        visitorEmail: json['visitor_email'] as String,
        status: json['status'] as String,
        lastMessageAt: DateTime.parse(json['last_message_at'] as String),
        latestMessage: json['latest_message'] as String?,
        unreadCount: json['unread_count'] as int,
      );

  final int id;
  final int reference;
  final String visitorName;
  final String visitorEmail;
  final String status;
  final DateTime lastMessageAt;
  final String? latestMessage;
  final int unreadCount;
}

class Conversation {
  const Conversation({
    required this.id,
    required this.reference,
    required this.visitorName,
    required this.visitorEmail,
    required this.status,
    required this.lastMessageAt,
    required this.messages,
  });

  factory Conversation.fromJson(Map<String, dynamic> json) => Conversation(
    id: json['id'] as int,
    reference: json['reference'] as int,
    visitorName: json['visitor_name'] as String,
    visitorEmail: json['visitor_email'] as String,
    status: json['status'] as String,
    lastMessageAt: DateTime.parse(json['last_message_at'] as String),
    messages: (json['messages'] as List<dynamic>)
        .map((item) => ChatMessage.fromJson(item as Map<String, dynamic>))
        .toList(),
  );

  final int id;
  final int reference;
  final String visitorName;
  final String visitorEmail;
  final String status;
  final DateTime lastMessageAt;
  final List<ChatMessage> messages;

  bool get isOpen => status == 'open';

  Conversation copyWith({String? status, List<ChatMessage>? messages}) =>
      Conversation(
        id: id,
        reference: reference,
        visitorName: visitorName,
        visitorEmail: visitorEmail,
        status: status ?? this.status,
        lastMessageAt: lastMessageAt,
        messages: messages ?? this.messages,
      );
}
