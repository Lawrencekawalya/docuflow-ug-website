import 'package:flutter/material.dart';

import 'src/app.dart';
import 'src/services/push_service.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await PushService.prepareBackgroundMessaging();
  runApp(const DocuFlowSupportApp());
}
