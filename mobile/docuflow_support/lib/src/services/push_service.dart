import 'dart:io';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

import 'api_client.dart';

@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  try {
    await Firebase.initializeApp();
  } on FirebaseException {
    // Firebase has not yet been configured on this build.
  }
}

class PushService {
  PushService._();

  static final instance = PushService._();
  static const _channel = AndroidNotificationChannel(
    'docuflow_chats',
    'Website chats',
    description: 'New DocuFlow website visitor messages',
    importance: Importance.high,
  );

  final _notifications = FlutterLocalNotificationsPlugin();
  bool _initialized = false;
  String? currentToken;
  void Function(int conversationId)? onConversationOpened;

  static Future<void> prepareBackgroundMessaging() async {
    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  }

  Future<void> initialize(ApiClient api, {required String deviceName}) async {
    if (_initialized) {
      if (currentToken != null) {
        await _register(api, currentToken!, deviceName);
      }
      return;
    }

    try {
      await Firebase.initializeApp();
      await _initializeLocalNotifications();

      final messaging = FirebaseMessaging.instance;
      await messaging.requestPermission(alert: true, badge: true, sound: true);
      currentToken = await messaging.getToken();
      if (currentToken != null) {
        await _register(api, currentToken!, deviceName);
      }

      messaging.onTokenRefresh.listen((token) async {
        currentToken = token;
        await _register(api, token, deviceName);
      });
      FirebaseMessaging.onMessage.listen(_showForegroundNotification);
      FirebaseMessaging.onMessageOpenedApp.listen(_openFromMessage);

      final initialMessage = await messaging.getInitialMessage();
      if (initialMessage != null) _openFromMessage(initialMessage);

      _initialized = true;
    } on FirebaseException {
      // The inbox remains usable until Firebase configuration is supplied.
    } on ApiException {
      // Registration will retry the next time the authenticated app starts.
    }
  }

  Future<void> _initializeLocalNotifications() async {
    const settings = InitializationSettings(
      android: AndroidInitializationSettings('@mipmap/ic_launcher'),
      iOS: DarwinInitializationSettings(
        requestAlertPermission: false,
        requestBadgePermission: false,
        requestSoundPermission: false,
      ),
    );
    await _notifications.initialize(
      settings: settings,
      onDidReceiveNotificationResponse: (response) {
        final id = int.tryParse(response.payload ?? '');
        if (id != null) onConversationOpened?.call(id);
      },
    );
    await _notifications
        .resolvePlatformSpecificImplementation<
          AndroidFlutterLocalNotificationsPlugin
        >()
        ?.createNotificationChannel(_channel);
  }

  Future<void> _register(ApiClient api, String token, String deviceName) =>
      api.registerDevice(
        fcmToken: token,
        platform: Platform.isIOS ? 'ios' : 'android',
        deviceName: deviceName,
      );

  Future<void> _showForegroundNotification(RemoteMessage message) async {
    final notification = message.notification;
    final conversationId = message.data['conversation_id'];
    if (notification == null || conversationId == null) return;

    await _notifications.show(
      id: message.messageId?.hashCode ?? DateTime.now().millisecondsSinceEpoch,
      title: notification.title,
      body: notification.body,
      notificationDetails: const NotificationDetails(
        android: AndroidNotificationDetails(
          'docuflow_chats',
          'Website chats',
          channelDescription: 'New DocuFlow website visitor messages',
          importance: Importance.high,
          priority: Priority.high,
          icon: '@mipmap/ic_launcher',
        ),
        iOS: DarwinNotificationDetails(presentSound: true),
      ),
      payload: conversationId,
    );
  }

  void _openFromMessage(RemoteMessage message) {
    final id = int.tryParse(message.data['conversation_id'] ?? '');
    if (id != null) onConversationOpened?.call(id);
  }
}
