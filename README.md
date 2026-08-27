# docuflow-ug-website
# DocuFlow UG

DocuFlow UG is a Laravel, Inertia and Vue website for a practical document-automation service aimed at accounting firms and document-heavy Ugandan businesses.

## Local setup

```bash
composer setup
composer dev
```

The public website is available at `/`; the existing authenticated application remains available at `/dashboard` after login.

## Demo-request delivery

Every valid demo request is saved to the `demo_requests` table before an email notification is queued. Configure a real recipient and mail transport before launch:

```dotenv
DOCUFLOW_LEADS_EMAIL=support@syntaxsystems.co
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=mail.syntaxsystems.co
MAIL_PORT=587
MAIL_USERNAME=support
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=support@syntaxsystems.co
MAIL_FROM_NAME="DocuFlow UG"
```

Set `MAIL_PASSWORD` only in the private environment file on the target machine; never commit it. This project reads `MAIL_SCHEME=tls` for SMTP transport encryption, not `MAIL_ENCRYPTION`.

Run a queue worker when `QUEUE_CONNECTION` is asynchronous:

```bash
php artisan queue:work --tries=3
```

The database remains the source of truth if notification delivery is temporarily unavailable.

## Public business configuration

The approved public commercial and contact values are the application defaults. They may be overridden in `.env` when the business changes them:

```dotenv
DOCUFLOW_CONTACT_EMAIL=support@syntaxsystems.co
DOCUFLOW_PHONE="+256 755400297"
DOCUFLOW_WHATSAPP_NUMBER="+256 778864614"

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

WhatsApp numbers should use international format. For example, a Ugandan number begins with `256` and contains digits only in the generated WhatsApp URL.

## Quality checks

```bash
composer ci:check
npm run build
```

The production build includes Inertia server-side rendering. In production,
the `docuflowug-ssr` service serves complete initial HTML to visitors and
crawlers while Vue hydrates it for client-side navigation.

Before grading or launch, submit a controlled request through the deployed form and verify both the saved database record and delivery to the configured email recipient.

See [the implementation plan](docs/implementationplan.md) for the full rubric-aligned delivery and acceptance criteria.

Production deployment instructions are in [the CI/CD deployment guide](docs/deployment.md).
