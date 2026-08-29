# DocuFlow Support mobile app

Flutter companion app for replying to DocuFlow website chats. It connects to
`https://docuflowug.syntaxsystems.co/api/mobile` by default and uses Firebase
Cloud Messaging for new-message notifications.

## Firebase activation

1. Create a Firebase project and add Android app
   `com.syntaxsystems.docuflow_support`.
2. Download `google-services.json` into `android/app/`. The Android build
   activates the Google Services plugin automatically when that file exists.
3. For iOS, register bundle ID `com.syntaxsystems.docuflowSupport`, download
   `GoogleService-Info.plist`, then add it to the Runner target in Xcode. Enable
   Push Notifications and Background Modes > Remote notifications for Runner.
4. In Firebase, create a service account with permission to send Cloud
   Messaging messages. On the VPS, save its JSON key outside every release,
   for example `/var/www/docuflowug/shared/firebase-service-account.json`.
5. Add these values to `/var/www/docuflowug/shared/.env`:

   ```dotenv
   FIREBASE_PROJECT_ID=your-firebase-project-id
   FIREBASE_CREDENTIALS=/var/www/docuflowug/shared/firebase-service-account.json
   ```

6. Refresh Laravel's cached configuration and restart the worker:

   ```bash
   cd /var/www/docuflowug/current
   sudo -u deployer php artisan optimize:clear
   sudo -u deployer php artisan optimize
   sudo systemctl restart docuflowug-queue.service
   ```

The Firebase client configuration files identify the mobile app and may be
kept with the mobile source. The server service-account JSON is a private key:
never commit it, copy it into a release, or send it through chat.

## Development

```bash
flutter pub get
dart format --output=none --set-exit-if-changed .
flutter analyze
flutter test
flutter build apk --debug
```

The debug APK is written to
`build/app/outputs/flutter-apk/app-debug.apk`. For an internal release build,
first configure a private Android signing key, then run `flutter build apk --release`.

Override the API for local development with:

```bash
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/mobile
```
