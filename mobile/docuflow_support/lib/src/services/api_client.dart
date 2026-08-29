import 'dart:convert';

import 'package:http/http.dart' as http;

import '../models/chat_models.dart';

class ApiException implements Exception {
  const ApiException(this.message, {this.statusCode});

  final String message;
  final int? statusCode;

  @override
  String toString() => message;
}

class LoginResult {
  const LoginResult({required this.token, required this.user});

  final String token;
  final SupportUser user;
}

class ConversationListResult {
  const ConversationListResult({
    required this.conversations,
    required this.unreadCount,
  });

  final List<ConversationSummary> conversations;
  final int unreadCount;
}

class ApiClient {
  ApiClient({http.Client? client}) : _client = client ?? http.Client();

  static const baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'https://docuflowug.syntaxsystems.co/api/mobile',
  );

  final http.Client _client;
  String? token;

  Future<LoginResult> login({
    required String email,
    required String password,
    required String deviceName,
  }) async {
    final json = await _request(
      'POST',
      '/login',
      body: {'email': email, 'password': password, 'device_name': deviceName},
      authenticated: false,
    );

    return LoginResult(
      token: json['token'] as String,
      user: SupportUser.fromJson(json['user'] as Map<String, dynamic>),
    );
  }

  Future<SupportUser> me() async {
    final json = await _request('GET', '/me');
    return SupportUser.fromJson(json['user'] as Map<String, dynamic>);
  }

  Future<void> logout({String? fcmToken}) async {
    await _request('POST', '/logout', body: {'fcm_token': fcmToken});
  }

  Future<void> registerDevice({
    required String fcmToken,
    required String platform,
    required String deviceName,
  }) async {
    await _request(
      'POST',
      '/devices',
      body: {
        'token': fcmToken,
        'platform': platform,
        'device_name': deviceName,
      },
    );
  }

  Future<ConversationListResult> conversations() async {
    final json = await _request('GET', '/conversations');
    return ConversationListResult(
      conversations: (json['conversations'] as List<dynamic>)
          .map(
            (item) =>
                ConversationSummary.fromJson(item as Map<String, dynamic>),
          )
          .toList(),
      unreadCount: json['unread_count'] as int,
    );
  }

  Future<Conversation> conversation(int id) async {
    final json = await _request('GET', '/conversations/$id');
    return Conversation.fromJson(json['conversation'] as Map<String, dynamic>);
  }

  Future<({String status, List<ChatMessage> messages})> messages(
    int conversationId, {
    required int after,
  }) async {
    final json = await _request(
      'GET',
      '/conversations/$conversationId/messages?after=$after',
    );
    return (
      status: json['status'] as String,
      messages: (json['messages'] as List<dynamic>)
          .map((item) => ChatMessage.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }

  Future<ChatMessage> reply(int conversationId, String message) async {
    final json = await _request(
      'POST',
      '/conversations/$conversationId/messages',
      body: {'message': message},
    );
    return ChatMessage.fromJson(json['message'] as Map<String, dynamic>);
  }

  Future<String> updateStatus(int conversationId, String status) async {
    final json = await _request(
      'PATCH',
      '/conversations/$conversationId/status',
      body: {'status': status},
    );
    return json['status'] as String;
  }

  Future<Map<String, dynamic>> _request(
    String method,
    String path, {
    Map<String, dynamic>? body,
    bool authenticated = true,
  }) async {
    final uri = Uri.parse('$baseUrl$path');
    final headers = <String, String>{
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    if (authenticated) {
      if (token == null) {
        throw const ApiException('You are not signed in.', statusCode: 401);
      }
      headers['Authorization'] = 'Bearer $token';
    }

    late http.Response response;
    final encodedBody = body == null ? null : jsonEncode(body);

    try {
      response = switch (method) {
        'GET' => await _client.get(uri, headers: headers),
        'POST' => await _client.post(uri, headers: headers, body: encodedBody),
        'PATCH' => await _client.patch(
          uri,
          headers: headers,
          body: encodedBody,
        ),
        _ => throw ArgumentError('Unsupported HTTP method: $method'),
      };
    } on http.ClientException {
      throw const ApiException(
        'Could not reach DocuFlow. Check your internet connection.',
      );
    }

    final decoded = response.body.isEmpty
        ? <String, dynamic>{}
        : jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode < 200 || response.statusCode >= 300) {
      final errors = decoded['errors'];
      String? validationMessage;
      if (errors is Map<String, dynamic> && errors.isNotEmpty) {
        final first = errors.values.first;
        if (first is List<dynamic> && first.isNotEmpty) {
          validationMessage = first.first.toString();
        }
      }

      throw ApiException(
        validationMessage ??
            decoded['message']?.toString() ??
            'The request could not be completed.',
        statusCode: response.statusCode,
      );
    }

    return decoded;
  }
}
