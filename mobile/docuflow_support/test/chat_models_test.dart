import 'package:docuflow_support/src/models/chat_models.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('parses a conversation and its messages from the Laravel API', () {
    final conversation = Conversation.fromJson({
      'id': 1,
      'reference': 1000,
      'visitor_name': 'Sarah Namara',
      'visitor_email': 'sarah@example.com',
      'status': 'open',
      'last_message_at': '2026-08-29T16:00:00+03:00',
      'messages': [
        {
          'id': 4,
          'sender_type': 'visitor',
          'body': 'Can you help?',
          'read_at': null,
          'created_at': '2026-08-29T16:00:00+03:00',
        },
      ],
    });

    expect(conversation.reference, 1000);
    expect(conversation.isOpen, isTrue);
    expect(conversation.messages.single.body, 'Can you help?');
    expect(conversation.messages.single.isSupport, isFalse);
  });
}
