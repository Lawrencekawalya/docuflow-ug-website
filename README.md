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
DOCUFLOW_LEADS_EMAIL=leads@your-approved-domain.example
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="DocuFlow UG"
```

Run a queue worker when `QUEUE_CONNECTION` is asynchronous:

```bash
php artisan queue:work --tries=3
```

The database remains the source of truth if notification delivery is temporarily unavailable.

## Public business configuration

The site deliberately does not invent commercial or contact information. Add the approved values to `.env` before launch:

```dotenv
DOCUFLOW_CONTACT_EMAIL=
DOCUFLOW_PHONE=
DOCUFLOW_WHATSAPP_NUMBER=

DOCUFLOW_STARTER_DOCUMENT_ALLOWANCE=
DOCUFLOW_GROWTH_MONTHLY_PRICE=
DOCUFLOW_GROWTH_SETUP_FEE=
DOCUFLOW_GROWTH_DOCUMENT_ALLOWANCE=
DOCUFLOW_PROFESSIONAL_MONTHLY_PRICE=
DOCUFLOW_PROFESSIONAL_SETUP_FEE=
DOCUFLOW_PROFESSIONAL_DOCUMENT_ALLOWANCE=
```

WhatsApp numbers should use international format. For example, a Ugandan number begins with `256` and contains digits only in the generated WhatsApp URL.

## Quality checks

```bash
composer ci:check
npm run build
```

Before grading or launch, submit a controlled request through the deployed form and verify both the saved database record and delivery to the configured email recipient.

See [the implementation plan](docs/implementationplan.md) for the full rubric-aligned delivery and acceptance criteria.
