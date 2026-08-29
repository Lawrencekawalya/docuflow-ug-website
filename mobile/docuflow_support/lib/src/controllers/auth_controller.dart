import 'dart:io';

import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../models/chat_models.dart';
import '../services/api_client.dart';

class AuthController extends ChangeNotifier {
  AuthController(this.api);

  static const _tokenKey = 'docuflow_support_token';
  static const _storage = FlutterSecureStorage(aOptions: AndroidOptions());

  final ApiClient api;
  SupportUser? user;
  bool isLoading = true;

  bool get isAuthenticated => user != null && api.token != null;
  String get deviceName => 'DocuFlow Support on ${Platform.operatingSystem}';

  Future<void> initialize() async {
    final savedToken = await _storage.read(key: _tokenKey);
    if (savedToken != null) {
      api.token = savedToken;
      try {
        user = await api.me();
      } on ApiException {
        api.token = null;
        await _storage.delete(key: _tokenKey);
      }
    }
    isLoading = false;
    notifyListeners();
  }

  Future<void> login(String email, String password) async {
    final result = await api.login(
      email: email.trim().toLowerCase(),
      password: password,
      deviceName: deviceName,
    );
    api.token = result.token;
    user = result.user;
    await _storage.write(key: _tokenKey, value: result.token);
    notifyListeners();
  }

  Future<void> logout({String? fcmToken}) async {
    try {
      await api.logout(fcmToken: fcmToken);
    } on ApiException {
      // Local sign-out must still succeed if the network is unavailable.
    }
    api.token = null;
    user = null;
    await _storage.delete(key: _tokenKey);
    notifyListeners();
  }
}
