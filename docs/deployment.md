# DocuFlow UG Production Deployment

This guide deploys `main` to `docuflowug.syntaxsystems.co` through GitHub Actions. CI must pass before deployment starts. Each deployment creates an immutable release, reuses shared state, runs migrations, atomically switches the `current` symlink, and restarts the queue worker.

## Architecture

```text
GitHub push to main
        ↓
CI: lint + types + static analysis + tests
        ↓
Build Vite client and SSR assets
        ↓
SSH upload to dedicated deployer account
        ↓
/var/www/docuflowug/releases/<commit-sha>
        ↓
Composer install → migrate → optimize
        ↓
/var/www/docuflowug/current (atomic symlink)
        ↓
Nginx + PHP-FPM + queue worker + Inertia SSR
```

The production `.env`, SQLite database, user-generated storage, logs, cache data and sessions live under `/var/www/docuflowug/shared` and survive releases.

## 1. Configure DNS

In the DNS manager for `syntaxsystems.co`, add:

| Type | Name | Value | TTL |
| --- | --- | --- | --- |
| A | `docuflowug` | `187.77.179.252` | 300 |

Do not add an `AAAA` record until IPv6 HTTP and firewall access have been verified. Remove any conflicting `A`, `AAAA`, or `CNAME` record for the same hostname.

Verify from the local computer:

```bash
dig +short A docuflowug.syntaxsystems.co
```

The result must be `187.77.179.252` before requesting the TLS certificate.

## 2. Create the CI/CD SSH key

Run this on the local computer, not on the VPS and not inside the repository:

```bash
ssh-keygen -t ed25519 -C "github-actions-docuflow" -f ~/.ssh/docuflowug_github_actions
```

This creates:

- `~/.ssh/docuflowug_github_actions` — private key for the GitHub secret;
- `~/.ssh/docuflowug_github_actions.pub` — public key installed for the VPS deployer.

Never commit either key and never paste the private key into chat.

## 3. Bootstrap the VPS

Connect as root:

```bash
ssh root@187.77.179.252
```

The VPS already runs PHP 8.4-FPM. Install the matching extensions without adding a second PHP runtime:

```bash
apt update
apt install -y nginx git unzip curl sqlite3 composer php8.4-cli php8.4-fpm php8.4-curl php8.4-mbstring php8.4-xml php8.4-zip php8.4-sqlite3 php8.4-bcmath php8.4-intl
```

Inertia v3 SSR requires Node 22 or newer. This server also hosts applications
that may depend on the existing system Node version, so install a verified
Node 22 runtime alongside it instead of replacing `/usr/bin/node`:

```bash
cd /tmp
curl -fsSLO https://nodejs.org/dist/latest-v22.x/node-v22.23.2-linux-x64.tar.xz
echo 'd60acfe00a2932254bb0ad20e01b0d74397a0875595de719654b214f4b03f307  node-v22.23.2-linux-x64.tar.xz' | sha256sum -c -
install -d /opt/node-v22.23.2
tar -xJf node-v22.23.2-linux-x64.tar.xz --strip-components=1 -C /opt/node-v22.23.2
ln -sfn /opt/node-v22.23.2 /opt/node22
/opt/node22/bin/node --version
```

The last command must report a `v22` release. The SSR systemd unit uses this
isolated binary explicitly.

Create an unprivileged deployment user and the shared directory structure:

```bash
id deployer >/dev/null 2>&1 || adduser --disabled-password --gecos "" deployer
usermod -aG www-data deployer

install -d -o deployer -g www-data -m 2775 /var/www/docuflowug
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/releases
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/shared
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/shared/database
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/shared/storage/app/public
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/shared/storage/framework/cache/data
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/shared/storage/framework/sessions
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/shared/storage/framework/views
install -d -o deployer -g www-data -m 2775 /var/www/docuflowug/shared/storage/logs
install -o deployer -g www-data -m 664 /dev/null /var/www/docuflowug/shared/database/database.sqlite
```

Install the deployment public key:

```bash
install -d -o deployer -g deployer -m 700 /home/deployer/.ssh
nano /home/deployer/.ssh/authorized_keys
```

Paste the single line from the local file `~/.ssh/docuflowug_github_actions.pub`, save it, then run:

```bash
chown deployer:deployer /home/deployer/.ssh/authorized_keys
chmod 600 /home/deployer/.ssh/authorized_keys
```

From the local computer, verify key-only access:

```bash
ssh -i ~/.ssh/docuflowug_github_actions deployer@187.77.179.252
```

## 4. Create the production environment

On the VPS, generate a Laravel application key:

```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Create the environment file:

```bash
nano /var/www/docuflowug/shared/.env
```

Use this template and replace every `CHANGE_ME` value:

```dotenv
APP_NAME="DocuFlow UG"
APP_ENV=production
APP_KEY=CHANGE_ME_WITH_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://docuflowug.syntaxsystems.co

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/docuflowug/shared/database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=docuflowug.syntaxsystems.co

CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=CHANGE_ME
MAIL_PORT=587
MAIL_USERNAME=CHANGE_ME
MAIL_PASSWORD=CHANGE_ME
MAIL_FROM_ADDRESS=CHANGE_ME
MAIL_FROM_NAME="DocuFlow UG"

DOCUFLOW_CONTACT_EMAIL=lawkawalya@gmail.com
DOCUFLOW_PHONE="+256 755400297"
DOCUFLOW_WHATSAPP_NUMBER="+256 778864614"
DOCUFLOW_LEADS_EMAIL=CHANGE_ME

DOCUFLOW_STARTER_DOCUMENT_ALLOWANCE=100
DOCUFLOW_GROWTH_MONTHLY_PRICE=300000
DOCUFLOW_GROWTH_SETUP_FEE=500000
DOCUFLOW_GROWTH_DOCUMENT_ALLOWANCE=300
DOCUFLOW_PROFESSIONAL_MONTHLY_PRICE=500000
DOCUFLOW_PROFESSIONAL_SETUP_FEE=750000
DOCUFLOW_PROFESSIONAL_DOCUMENT_ALLOWANCE=750
DOCUFLOW_OVERAGE_POLICY="Additional documents above the monthly allowance are charged at UGX 1,000 per document. Customers are notified before recurring overage charges are applied."
DOCUFLOW_CANCELLATION_POLICY="Month-to-month subscription. No long-term contract is required. Customers may cancel before the next billing cycle. Setup fees are one-time and non-refundable once implementation and configuration work has started."
```

Protect it:

```bash
chown deployer:www-data /var/www/docuflowug/shared/.env
chmod 640 /var/www/docuflowug/shared/.env
```

Do not proceed with literal `CHANGE_ME` values in production.

## 5. Install the Nginx and queue configuration

On the VPS:

```bash
git clone --depth=1 https://github.com/Lawrencekawalya/docuflow-ug-website.git /tmp/docuflow-bootstrap
cp /tmp/docuflow-bootstrap/deploy/nginx/docuflowug.syntaxsystems.co.conf /etc/nginx/sites-available/docuflowug.syntaxsystems.co
ln -s /etc/nginx/sites-available/docuflowug.syntaxsystems.co /etc/nginx/sites-enabled/docuflowug.syntaxsystems.co
cp /tmp/docuflow-bootstrap/deploy/systemd/docuflowug-queue.service /etc/systemd/system/docuflowug-queue.service
cp /tmp/docuflow-bootstrap/deploy/systemd/docuflowug-ssr.service /etc/systemd/system/docuflowug-ssr.service
nginx -t
systemctl reload nginx
systemctl daemon-reload
```

If the enabled-site symlink already exists, do not recreate it. Check `ufw status` and ensure ports 22, 80 and 443 are permitted without changing rules required by the other hosted sites.

## 6. Add GitHub production secrets

First verify the VPS SSH fingerprint. On the VPS:

```bash
ssh-keygen -lf /etc/ssh/ssh_host_ed25519_key.pub
```

On the local computer:

```bash
ssh-keyscan -t ed25519 187.77.179.252 | ssh-keygen -lf -
```

The fingerprints must match. Then capture the known-host entry locally:

```bash
ssh-keyscan -H -t ed25519 187.77.179.252
```

In the GitHub repository, open **Settings → Environments → New environment**, create `production`, then add these environment secrets:

| Secret | Value |
| --- | --- |
| `DEPLOY_SSH_KEY` | Entire contents of `~/.ssh/docuflowug_github_actions` |
| `DEPLOY_KNOWN_HOSTS` | Complete output of the hashed `ssh-keyscan` command |

GitHub documents environment and repository secrets under Settings → Secrets and variables → Actions. Environment secrets are used here so production access can be reviewed separately from CI.

## 7. Trigger the first deployment

Commit and push the deployment files:

```bash
git add .github/workflows/tests.yml deploy docs/deployment.md app/Http/Controllers/SitemapController.php routes/web.php
git commit -m "ci: deploy DocuFlow to production"
git push origin main
```

Open the repository's **Actions** tab. The `tests` workflow must complete the CI job before `Deploy to production` starts.

After it succeeds, verify on the VPS:

```bash
readlink -f /var/www/docuflowug/current
sudo -u deployer php /var/www/docuflowug/current/artisan about --only=environment
curl -H 'Host: docuflowug.syntaxsystems.co' http://127.0.0.1/up
```

The health endpoint should return `Application up`.

## 8. Start the queue worker and SSR server

After the first release exists:

```bash
systemctl enable --now docuflowug-queue docuflowug-ssr
systemctl status docuflowug-queue --no-pager
systemctl status docuflowug-ssr --no-pager
sudo -u deployer php /var/www/docuflowug/current/artisan inertia:check-ssr
```

The shared storage tree must remain writable by both PHP-FPM and the deployer.
Apply the initial ownership and permissions once as root:

```bash
chown -R deployer:www-data /var/www/docuflowug/shared/storage
find /var/www/docuflowug/shared/storage -type d -exec chmod 2775 {} +
find /var/www/docuflowug/shared/storage -type f -exec chmod 0664 {} +
```

View worker logs with:

```bash
journalctl -u docuflowug-queue -n 100 --no-pager
journalctl -u docuflowug-ssr -n 100 --no-pager
```

## 9. Enable HTTPS

Only continue after DNS resolves to the VPS and HTTP works publicly. Install Certbot using its current official Nginx instructions, then request the certificate:

```bash
certbot --nginx -d docuflowug.syntaxsystems.co --redirect
certbot renew --dry-run
```

Verify:

```bash
curl -I https://docuflowug.syntaxsystems.co
curl https://docuflowug.syntaxsystems.co/up
```

## 10. End-to-end lead test

1. Open `https://docuflowug.syntaxsystems.co/contact` on a real phone.
2. Submit a clearly labelled controlled test request.
3. Confirm the success message appears.
4. On the VPS, confirm a row exists in `demo_requests`.
5. Confirm the queued job was processed and the message reached `DOCUFLOW_LEADS_EMAIL`.
6. Check failed jobs and application logs:

```bash
sudo -u deployer php /var/www/docuflowug/current/artisan queue:failed
tail -n 100 /var/www/docuflowug/shared/storage/logs/laravel.log
```

This real delivery check is required by the grading rubric; an automated notification test is not a substitute.

## Normal deployments

Every later push to `main` automatically runs CI and deploys only if CI passes. The five newest releases are retained. Each production build includes the browser bundle and the Inertia SSR bundle. Deployment stops the old SSR process after the atomic release switch, and systemd starts it again against the new release.

After editing the production `.env`, refresh cached configuration and restart the worker:

```bash
sudo -u deployer php /var/www/docuflowug/current/artisan optimize
sudo -u deployer php /var/www/docuflowug/current/artisan queue:restart
sudo -u deployer php /var/www/docuflowug/current/artisan inertia:stop-ssr
```

## Rollback

List releases and choose the exact previous release directory:

```bash
ls -1dt /var/www/docuflowug/releases/*
```

Switch atomically as root, replacing `PREVIOUS_RELEASE_SHA` with an existing directory name:

```bash
ln -s /var/www/docuflowug/releases/PREVIOUS_RELEASE_SHA /var/www/docuflowug/current.next
mv -Tf /var/www/docuflowug/current.next /var/www/docuflowug/current
sudo -u deployer php /var/www/docuflowug/current/artisan optimize
sudo -u deployer php /var/www/docuflowug/current/artisan queue:restart
```

Code rollback does not automatically reverse database migrations. Deploy backward-compatible migrations and use explicit corrective migrations when necessary.
