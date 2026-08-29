import 'package:flutter/material.dart';

import 'controllers/auth_controller.dart';
import 'pages/conversation_page.dart';
import 'pages/conversations_page.dart';
import 'pages/login_page.dart';
import 'services/api_client.dart';
import 'services/push_service.dart';

class DocuFlowSupportApp extends StatefulWidget {
  const DocuFlowSupportApp({super.key});

  @override
  State<DocuFlowSupportApp> createState() => _DocuFlowSupportAppState();
}

class _DocuFlowSupportAppState extends State<DocuFlowSupportApp> {
  final _navigatorKey = GlobalKey<NavigatorState>();
  late final ApiClient _api;
  late final AuthController _auth;
  int? _pendingConversationId;

  @override
  void initState() {
    super.initState();
    _api = ApiClient();
    _auth = AuthController(_api)..addListener(_onAuthChanged);
    PushService.instance.onConversationOpened = _openConversation;
    _initialize();
  }

  Future<void> _initialize() async {
    await _auth.initialize();
    if (_auth.isAuthenticated) {
      await _initializePush();
      _openPendingConversation();
    }
  }

  Future<void> _initializePush() =>
      PushService.instance.initialize(_api, deviceName: _auth.deviceName);

  void _onAuthChanged() {
    if (mounted) setState(() {});
  }

  void _openConversation(int conversationId) {
    if (!_auth.isAuthenticated) return;
    final navigator = _navigatorKey.currentState;
    if (navigator == null) {
      _pendingConversationId = conversationId;
      return;
    }

    navigator.push(
      MaterialPageRoute<void>(
        builder: (_) =>
            ConversationPage(api: _api, conversationId: conversationId),
      ),
    );
  }

  void _openPendingConversation() {
    final id = _pendingConversationId;
    if (id == null) return;

    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;
      _pendingConversationId = null;
      _openConversation(id);
    });
  }

  @override
  void dispose() {
    _auth.removeListener(_onAuthChanged);
    _auth.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    const seed = Color(0xFF2563EB);
    final lightScheme = ColorScheme.fromSeed(seedColor: seed);
    final darkScheme = ColorScheme.fromSeed(
      seedColor: seed,
      brightness: Brightness.dark,
    );

    return MaterialApp(
      navigatorKey: _navigatorKey,
      debugShowCheckedModeBanner: false,
      title: 'DocuFlow Support',
      themeMode: ThemeMode.system,
      theme: _theme(lightScheme),
      darkTheme: _theme(darkScheme),
      home: _auth.isLoading
          ? const _SplashPage()
          : _auth.isAuthenticated
          ? ConversationsPage(auth: _auth)
          : LoginPage(auth: _auth, onSignedIn: _initializePush),
    );
  }

  ThemeData _theme(ColorScheme scheme) => ThemeData(
    colorScheme: scheme,
    useMaterial3: true,
    scaffoldBackgroundColor: scheme.surface,
    appBarTheme: AppBarTheme(
      elevation: 0,
      scrolledUnderElevation: 1,
      backgroundColor: scheme.surface,
      foregroundColor: scheme.onSurface,
    ),
    cardTheme: CardThemeData(
      elevation: 0,
      shape: RoundedRectangleBorder(
        side: BorderSide(color: scheme.outlineVariant),
        borderRadius: BorderRadius.circular(18),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: scheme.surfaceContainerLow,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(15),
        borderSide: BorderSide(color: scheme.outlineVariant),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(15),
        borderSide: BorderSide(color: scheme.outlineVariant),
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        minimumSize: const Size.fromHeight(52),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        textStyle: const TextStyle(fontWeight: FontWeight.w800),
      ),
    ),
  );
}

class _SplashPage extends StatelessWidget {
  const _SplashPage();

  @override
  Widget build(BuildContext context) =>
      const Scaffold(body: Center(child: CircularProgressIndicator()));
}
