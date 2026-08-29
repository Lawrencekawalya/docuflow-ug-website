import 'dart:async';

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../models/chat_models.dart';
import '../services/api_client.dart';

class ConversationPage extends StatefulWidget {
  const ConversationPage({
    required this.api,
    required this.conversationId,
    super.key,
  });

  final ApiClient api;
  final int conversationId;

  @override
  State<ConversationPage> createState() => _ConversationPageState();
}

class _ConversationPageState extends State<ConversationPage> {
  final _reply = TextEditingController();
  final _scrollController = ScrollController();
  Conversation? _conversation;
  bool _loading = true;
  bool _sending = false;
  String? _error;
  Timer? _poller;

  @override
  void initState() {
    super.initState();
    _load();
    _poller = Timer.periodic(const Duration(seconds: 4), (_) => _poll());
  }

  @override
  void dispose() {
    _poller?.cancel();
    _reply.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    try {
      final conversation = await widget.api.conversation(widget.conversationId);
      if (mounted) {
        setState(() {
          _conversation = conversation;
          _error = null;
          _loading = false;
        });
        _scrollToBottom();
      }
    } on ApiException catch (error) {
      if (mounted) {
        setState(() {
          _error = error.message;
          _loading = false;
        });
      }
    }
  }

  Future<void> _poll() async {
    final conversation = _conversation;
    if (conversation == null) return;
    try {
      final after = conversation.messages.isEmpty
          ? 0
          : conversation.messages.last.id;
      final result = await widget.api.messages(conversation.id, after: after);
      if (!mounted ||
          (result.messages.isEmpty && result.status == conversation.status)) {
        return;
      }
      setState(() {
        _conversation = conversation.copyWith(
          status: result.status,
          messages: [...conversation.messages, ...result.messages],
        );
      });
      if (result.messages.isNotEmpty) _scrollToBottom();
    } on ApiException {
      // A later poll or manual action will retry.
    }
  }

  Future<void> _send() async {
    final body = _reply.text.trim();
    final conversation = _conversation;
    if (body.isEmpty || conversation == null || _sending) return;
    setState(() {
      _sending = true;
      _error = null;
    });
    try {
      final message = await widget.api.reply(conversation.id, body);
      if (mounted) {
        setState(() {
          _conversation = conversation.copyWith(
            messages: [...conversation.messages, message],
          );
          _reply.clear();
        });
        _scrollToBottom();
      }
    } on ApiException catch (error) {
      if (mounted) setState(() => _error = error.message);
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  Future<void> _toggleStatus() async {
    final conversation = _conversation;
    if (conversation == null) return;
    final status = conversation.isOpen ? 'closed' : 'open';
    try {
      final updated = await widget.api.updateStatus(conversation.id, status);
      if (mounted) {
        setState(() {
          _conversation = conversation.copyWith(status: updated);
        });
      }
    } on ApiException catch (error) {
      if (mounted) setState(() => _error = error.message);
    }
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 250),
          curve: Curves.easeOut,
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final conversation = _conversation;
    return Scaffold(
      appBar: AppBar(
        title: conversation == null
            ? const Text('Conversation')
            : Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    conversation.visitorName,
                    style: const TextStyle(
                      fontSize: 17,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                  Text(
                    '#${conversation.reference} · ${conversation.status}',
                    style: const TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
        actions: [
          if (conversation != null)
            TextButton.icon(
              onPressed: _toggleStatus,
              icon: Icon(
                conversation.isOpen
                    ? Icons.check_circle_outline_rounded
                    : Icons.refresh_rounded,
              ),
              label: Text(conversation.isOpen ? 'Close' : 'Reopen'),
            ),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : conversation == null
          ? Center(child: Text(_error ?? 'Conversation not found.'))
          : Column(
              children: [
                Material(
                  color: Theme.of(context).colorScheme.surfaceContainerLow,
                  child: Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 10,
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.mail_outline_rounded, size: 17),
                        const SizedBox(width: 8),
                        Expanded(child: Text(conversation.visitorEmail)),
                      ],
                    ),
                  ),
                ),
                Expanded(
                  child: ListView.builder(
                    controller: _scrollController,
                    padding: const EdgeInsets.all(16),
                    itemCount: conversation.messages.length,
                    itemBuilder: (context, index) {
                      final message = conversation.messages[index];
                      return Align(
                        alignment: message.isSupport
                            ? Alignment.centerRight
                            : Alignment.centerLeft,
                        child: Container(
                          constraints: const BoxConstraints(maxWidth: 320),
                          margin: const EdgeInsets.symmetric(vertical: 5),
                          padding: const EdgeInsets.fromLTRB(14, 10, 14, 8),
                          decoration: BoxDecoration(
                            color: message.isSupport
                                ? const Color(0xFF2563EB)
                                : Theme.of(
                                    context,
                                  ).colorScheme.surfaceContainerHighest,
                            borderRadius: BorderRadius.only(
                              topLeft: const Radius.circular(18),
                              topRight: const Radius.circular(18),
                              bottomLeft: Radius.circular(
                                message.isSupport ? 18 : 5,
                              ),
                              bottomRight: Radius.circular(
                                message.isSupport ? 5 : 18,
                              ),
                            ),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text(
                                message.body,
                                style: TextStyle(
                                  color: message.isSupport
                                      ? Colors.white
                                      : null,
                                  fontSize: 15,
                                  height: 1.35,
                                ),
                              ),
                              const SizedBox(height: 5),
                              Text(
                                DateFormat(
                                  'HH:mm',
                                ).format(message.createdAt.toLocal()),
                                style: TextStyle(
                                  color: message.isSupport
                                      ? Colors.blue.shade100
                                      : Theme.of(
                                          context,
                                        ).colorScheme.onSurfaceVariant,
                                  fontSize: 10,
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
                if (_error != null)
                  Padding(
                    padding: const EdgeInsets.fromLTRB(16, 6, 16, 0),
                    child: Text(
                      _error!,
                      style: const TextStyle(color: Colors.red),
                    ),
                  ),
                SafeArea(
                  top: false,
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
                    child: conversation.isOpen
                        ? Row(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Expanded(
                                child: TextField(
                                  controller: _reply,
                                  minLines: 1,
                                  maxLines: 5,
                                  textCapitalization:
                                      TextCapitalization.sentences,
                                  decoration: const InputDecoration(
                                    hintText: 'Write a reply…',
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              IconButton.filled(
                                onPressed: _sending ? null : _send,
                                icon: _sending
                                    ? const SizedBox.square(
                                        dimension: 18,
                                        child: CircularProgressIndicator(
                                          strokeWidth: 2,
                                        ),
                                      )
                                    : const Icon(Icons.send_rounded),
                              ),
                            ],
                          )
                        : Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Text('This conversation is closed.'),
                              TextButton(
                                onPressed: _toggleStatus,
                                child: const Text('Reopen'),
                              ),
                            ],
                          ),
                  ),
                ),
              ],
            ),
    );
  }
}
