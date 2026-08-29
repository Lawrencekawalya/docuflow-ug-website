import 'dart:async';

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../controllers/auth_controller.dart';
import '../models/chat_models.dart';
import '../services/api_client.dart';
import '../services/push_service.dart';
import '../widgets/docuflow_mark.dart';
import 'conversation_page.dart';

class ConversationsPage extends StatefulWidget {
  const ConversationsPage({required this.auth, super.key});

  final AuthController auth;

  @override
  State<ConversationsPage> createState() => _ConversationsPageState();
}

class _ConversationsPageState extends State<ConversationsPage> {
  List<ConversationSummary> _conversations = const [];
  bool _loading = true;
  String? _error;
  Timer? _poller;

  @override
  void initState() {
    super.initState();
    _load();
    _poller = Timer.periodic(
      const Duration(seconds: 10),
      (_) => _load(silent: true),
    );
  }

  @override
  void dispose() {
    _poller?.cancel();
    super.dispose();
  }

  Future<void> _load({bool silent = false}) async {
    if (!silent && mounted) setState(() => _loading = true);
    try {
      final result = await widget.auth.api.conversations();
      if (mounted) {
        setState(() {
          _conversations = result.conversations;
          _error = null;
        });
      }
    } on ApiException catch (error) {
      if (mounted && !silent) setState(() => _error = error.message);
    } finally {
      if (mounted && !silent) setState(() => _loading = false);
    }
  }

  Future<void> _open(int id) async {
    await Navigator.of(context).push(
      MaterialPageRoute<void>(
        builder: (_) =>
            ConversationPage(api: widget.auth.api, conversationId: id),
      ),
    );
    await _load(silent: true);
  }

  Future<void> _logout() =>
      widget.auth.logout(fcmToken: PushService.instance.currentToken);

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(
      toolbarHeight: 76,
      title: const DocuFlowWordmark(),
      actions: [
        PopupMenuButton<String>(
          onSelected: (value) {
            if (value == 'logout') _logout();
          },
          itemBuilder: (_) => const [
            PopupMenuItem(value: 'logout', child: Text('Log out')),
          ],
          icon: CircleAvatar(
            child: Text(
              widget.auth.user?.name.characters.first.toUpperCase() ?? 'S',
            ),
          ),
        ),
        const SizedBox(width: 8),
      ],
    ),
    body: RefreshIndicator(
      onRefresh: _load,
      child: CustomScrollView(
        slivers: [
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 22, 20, 10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Website conversations',
                    style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'New visitor messages appear automatically.',
                    style: TextStyle(
                      color: Theme.of(context).colorScheme.onSurfaceVariant,
                    ),
                  ),
                ],
              ),
            ),
          ),
          if (_loading)
            const SliverFillRemaining(
              child: Center(child: CircularProgressIndicator()),
            )
          else if (_error != null)
            SliverFillRemaining(
              hasScrollBody: false,
              child: _MessageState(
                icon: Icons.cloud_off_rounded,
                title: 'Could not load conversations',
                message: _error!,
                action: _load,
              ),
            )
          else if (_conversations.isEmpty)
            const SliverFillRemaining(
              hasScrollBody: false,
              child: _MessageState(
                icon: Icons.chat_bubble_outline_rounded,
                title: 'No conversations yet',
                message: 'New website chats will appear here.',
              ),
            )
          else
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(12, 8, 12, 28),
              sliver: SliverList.builder(
                itemCount: _conversations.length,
                itemBuilder: (context, index) {
                  final conversation = _conversations[index];
                  return Card(
                    margin: const EdgeInsets.symmetric(vertical: 5),
                    child: ListTile(
                      contentPadding: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 9,
                      ),
                      onTap: () => _open(conversation.id),
                      leading: CircleAvatar(
                        backgroundColor: const Color(0xFFDBEAFE),
                        foregroundColor: const Color(0xFF1D4ED8),
                        child: Text(
                          conversation.visitorName.characters.first
                              .toUpperCase(),
                          style: const TextStyle(fontWeight: FontWeight.w800),
                        ),
                      ),
                      title: Row(
                        children: [
                          Expanded(
                            child: Text(
                              conversation.visitorName,
                              style: const TextStyle(
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                          ),
                          Text(
                            DateFormat(
                              'HH:mm',
                            ).format(conversation.lastMessageAt.toLocal()),
                            style: Theme.of(context).textTheme.bodySmall,
                          ),
                        ],
                      ),
                      subtitle: Padding(
                        padding: const EdgeInsets.only(top: 5),
                        child: Row(
                          children: [
                            Expanded(
                              child: Text(
                                conversation.latestMessage ?? 'No messages',
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            if (conversation.unreadCount > 0)
                              Container(
                                margin: const EdgeInsets.only(left: 8),
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 3,
                                ),
                                decoration: BoxDecoration(
                                  color: const Color(0xFF2563EB),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Text(
                                  '${conversation.unreadCount}',
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 11,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                              ),
                          ],
                        ),
                      ),
                      trailing: const Icon(Icons.chevron_right_rounded),
                    ),
                  );
                },
              ),
            ),
        ],
      ),
    ),
  );
}

class _MessageState extends StatelessWidget {
  const _MessageState({
    required this.icon,
    required this.title,
    required this.message,
    this.action,
  });

  final IconData icon;
  final String title;
  final String message;
  final Future<void> Function()? action;

  @override
  Widget build(BuildContext context) => Center(
    child: Padding(
      padding: const EdgeInsets.all(32),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 54, color: Theme.of(context).colorScheme.primary),
          const SizedBox(height: 16),
          Text(title, style: Theme.of(context).textTheme.titleLarge),
          const SizedBox(height: 8),
          Text(message, textAlign: TextAlign.center),
          if (action != null) ...[
            const SizedBox(height: 20),
            FilledButton(onPressed: action, child: const Text('Try again')),
          ],
        ],
      ),
    ),
  );
}
