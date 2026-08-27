# DocuFlow UG Website Implementation Plan

## 1. Product Understanding

DocuFlow UG needs a polished, client-facing B2B website that sells a practical document-processing service to accounting firms and document-heavy Ugandan businesses. It is not an automation dashboard, an n8n showcase, or a generic AI landing page.

The website must help a visitor quickly understand:

- what DocuFlow UG does;
- who it is designed for;
- how documents move from intake to usable structured data;
- how the service saves time, reduces errors, and improves visibility;
- what the service costs in UGX;
- why the service can be trusted; and
- how to request a real demonstration.

The finished public website will have five primary pages:

1. Home (`/`)
2. How It Works (`/how-it-works`)
3. Pricing (`/pricing`)
4. About (`/about`)
5. Contact / Request Demo (`/contact`)

Every page will retain a prominent Request Demo action and end with a relevant conversion section.

## 2. Grading Target and Rubric Traceability

The implementation target is **25/25**. Rubric compliance is a release requirement, not an optional polish stage.

| Scoring area | Marks | Evidence required for full credit |
| --- | ---: | --- |
| Professional design and branding | 10 | Original DocuFlow UG logo/identity, clear visual hierarchy, consistent typography/colors/components, credible business presentation, quality original visuals, and a polished smartphone experience |
| Required pages and sections | 10 | Complete Home, How It Works, Pricing, About/Trust, and Contact experiences with all required content, explicit UGX pricing, customer-focused language, genuine claims, and strong demo CTAs |
| Working contact and lead capture | 5 | Usable name/email/message-capable form, professional email, phone/WhatsApp, clear next-step explanation, persistent lead record, and proof from a real end-to-end delivery test |

### Rubric audit checkpoints

Before implementation:

- inventory existing routes, pages, layouts, UI primitives, authentication features, branding assets, and tests;
- identify what can be retained, adapted, or must be replaced;
- capture current build and test results as the regression baseline; and
- compare all proposed pricing, positioning, claims, and capabilities with the final Business Projection and working MVP.

Before submission:

- evaluate every rubric line item on both desktop and a real smartphone-sized viewport;
- capture screenshots or other review evidence for each required page and mobile state;
- submit the production-configured demo form with a controlled test lead;
- verify the saved database record and confirm that the lead reached the intended email, CRM, or workflow destination;
- confirm that email, telephone, and WhatsApp links use final professional contact details; and
- perform a final Business Projection/MVP consistency review, recording and correcting every mismatch before grading.

## 3. Existing Technical Foundation

The implementation will build on the current application rather than replace its stack:

- Laravel 13 and PHP 8.4+
- Inertia.js 3
- Vue 3 with TypeScript
- Tailwind CSS 4
- Wayfinder-generated typed routes
- Existing reusable UI primitives and Lucide icons
- Fortify authentication and the existing authenticated dashboard/settings area
- PHPUnit, Laravel Pint, Larastan, ESLint, Prettier, and `vue-tsc`

The public marketing experience will use a separate layout and component family so that it does not become coupled to the authenticated application shell. Existing authentication and dashboard routes will remain functional but will not control the marketing-site design.

## 4. Key Product and Content Decisions

### Confirmed

- The primary conversion is a demo request, not self-service signup.
- The main audience is Ugandan SMEs, accounting firms, and document-heavy teams.
- The voice must be practical, credible, local, and nontechnical.
- The five-step product story is Receive, Read, Extract/Structure, Validate, and Deliver.
- Research statistics must be presented with their sample size and without exaggeration.
- Starter pricing is supported at **UGX 150,000/month plus UGX 500,000 setup**.
- Starter includes 100 documents/month; Growth is UGX 300,000/month plus UGX 500,000 setup for 300 documents/month; Professional is UGX 500,000/month plus UGX 750,000 setup for 750 documents/month.
- Overage is UGX 1,000 per additional document, with notice before recurring overage charges are applied.
- Plans are month-to-month and may be cancelled before the next billing cycle; setup fees become non-refundable once implementation and configuration work begins.
- Public contact details are `lawkawalya@gmail.com`, `+256 755400297`, and WhatsApp `+256 778864614`.
- Security wording must describe real practices and avoid unsupported certification or absolute-security claims.
- WhatsApp must be a prominent contact option, especially on mobile.
- Runtime and production builds must not depend on remote font downloads. Use the current system font stack or a locally hosted font asset.

### Required before production launch

The following values must be supplied and approved before publishing the site:

- final founder biography and any approved business address;
- destination email, CRM, or automation webhook for demo requests; and
- privacy-policy wording and data-retention period for submitted leads.

No invented prices, contact details, customer logos, testimonials, certifications, or performance claims will be placed on the live site.

## 5. Information Architecture and Routes

Add named public routes in `routes/web.php`:

| Method | URL | Route name | Purpose |
| --- | --- | --- | --- |
| GET | `/` | `home` | Main sales page |
| GET | `/how-it-works` | `how-it-works` | Process and reliability explanation |
| GET | `/pricing` | `pricing` | Transparent UGX packages and FAQ |
| GET | `/about` | `about` | Story, research, founder, and trust |
| GET | `/contact` | `contact` | Demo request form and direct contact options |
| POST | `/demo-requests` | `demo-requests.store` | Validate and persist demo requests |

Keep authenticated routes such as `/dashboard` separate and protected by their existing middleware. Do not expose Dashboard, Login, or Signup as primary marketing navigation unless the commercial product later requires a customer portal.

## 6. Proposed Frontend Structure

Create a reusable marketing layer under `resources/js`:

```text
layouts/
└── MarketingLayout.vue

components/marketing/
├── MarketingHeader.vue
├── MobileNavigation.vue
├── MarketingFooter.vue
├── BrandMark.vue
├── SectionHeading.vue
├── DemoCta.vue
├── WorkflowDiagram.vue
├── ProductVisual.vue
├── BenefitCard.vue
├── ResearchStat.vue
├── PricingCard.vue
├── FaqList.vue
└── ContactOptions.vue

pages/
├── Home.vue
├── HowItWorks.vue
├── Pricing.vue
├── About.vue
└── Contact.vue
```

Shared content that appears in multiple places—navigation items, plan features, contact details, research figures, and workflow steps—should be defined once in a typed configuration module or passed from Laravel configuration. Avoid duplicating business facts across pages.

Use Wayfinder route helpers for internal links and form submission. Use Inertia's `<Head>` component for page titles, descriptions, canonical metadata, and social-sharing metadata.

## 7. Visual and Interaction Direction

The supplied `theme.jpeg` is a directional reference, not an asset to copy. The implementation should borrow its strong spacing, clean hierarchy, blue-accented interface, and product-led hero composition while establishing an original DocuFlow UG identity.

### Design system

- A restrained palette led by trustworthy blue, deep navy text, white/off-white surfaces, and limited success/attention colors.
- Strong, readable typography using system or self-hosted fonts.
- Spacious sections with a consistent maximum content width.
- Rounded cards and controls with subtle borders and shadows rather than excessive glass effects or gradients.
- Original document/workflow illustrations built from HTML/CSS/SVG and actual product concepts.
- Motion limited to useful entrance, hover, menu, and process-flow feedback, with `prefers-reduced-motion` support.
- Consistent primary, secondary, text-link, and WhatsApp action styles.

### Responsive behavior

- Desktop navigation becomes an accessible hamburger/sheet menu on smaller screens.
- CTAs remain at least 44px high and easy to tap.
- Pricing cards stack cleanly.
- Workflow diagrams switch to a vertical sequence.
- Contact fields become a single column.
- Long labels and UGX prices do not overflow.
- No horizontal scrolling at 360px, 390px, or 430px.

### Avoid

- robot, glowing-brain, or hologram imagery;
- fake application screenshots or fake client logos;
- excessive gradients and decorative animation;
- n8n terminology, webhooks, JSON, nodes, or tokens in primary sales copy; and
- unsupported claims such as “100% accurate,” “100% secure,” or certified compliance.

## 8. Page-by-Page Implementation

### Phase A: Shared marketing foundation

1. Establish brand tokens, page spacing, typography, button variants, focus states, and reusable section patterns in `resources/css/app.css`.
2. Build `MarketingLayout.vue` with skip link, sticky header, desktop/mobile navigation, footer, contact links, and persistent Request Demo CTA.
3. Create an original DocuFlow brand mark suitable for header, footer, favicon, and social preview use.
4. Add shared workflow and product visuals that communicate:
   `Document → AI-assisted processing → validation → structured result → business destination`.
5. Add page metadata helpers and default social preview metadata.

### Phase B: Homepage (`/`)

Replace the starter Welcome page with these sections:

1. **Hero** — approved headline, supporting copy, Request a Free Demo and See How It Works actions, plus an original product/workflow visual.
2. **Problem** — manual entry, human error, slow processing, and poor tracking.
3. **Research proof** — carefully state that all five interviewed businesses reported monthly document-processing problems.
4. **Solution flow** — Receive, Read, Extract, Validate, Deliver.
5. **Benefits** — save staff time, reduce manual errors, process faster, and keep visibility.
6. **Research/trust statistics** — 5 interviewed, 3.8/5 automation interest, 5/5 monthly problems, and 4/5 stated potential paying interest. Clearly label these as initial research, not existing customers.
7. **Security** — controlled access, secure credential handling, validation, monitoring, and failure handling using measured language.
8. **Final CTA** — invite the visitor to demonstrate a realistic document from their workflow.

### Phase C: How It Works (`/how-it-works`)

1. Hero explaining the five-step journey in nontechnical language.
2. Illustrated five-stage process:
   - Submit Your Document
   - DocuFlow Reads It
   - Information Is Structured
   - Processing Is Checked
   - Results Reach Your Workflow
3. Responsive workflow diagram showing supported inputs, DocuFlow processing, and possible destinations.
4. “What happens when something goes wrong?” section covering monitored failures, records, appropriate retries, and notifications in customer language.
5. Use-case cards for invoice processing, receipt processing, business-document intake, and document routing.
6. Request Demo CTA.

### Phase D: Pricing (`/pricing`)

1. Present three comparable, volume-based cards: Starter, Growth, and Professional.
2. Clearly separate monthly service cost, one-time setup fee, document allowance, number of workflows, integrations, monitoring/reporting, and support level.
3. Highlight Starter without implying that higher tiers are unavailable.
4. Add “Every plan includes” for credential handling, monitoring, error handling, AI usage awareness, onboarding, and support.
5. Add a pilot/demo section focused on evaluating accuracy, processing time, and fit.
6. Add an accessible FAQ for setup fees, changing plans, overages, and contract policy.
7. Do not publish the page with “Contact us for price” placeholders. Final tier values are a release gate.

### Phase E: About (`/about`)

1. Hero: “Local Automation Built for Practical Business Problems.”
2. Concise story connecting DocuFlow UG to document-processing problems experienced by Ugandan SMEs.
3. Three approach principles: start with the business problem, prove value before scaling, and keep humans in control.
4. Research section describing the five Mbarara interviews and how the findings influenced pricing, security, reliability, and product priorities.
5. Founder section for Lawrence Kawalya with an approved, factual biography and professional image only if supplied.
6. Small technology section mentioning n8n, AI APIs, secure credential management, and monitored workflow infrastructure.
7. Contact CTA.

### Phase F: Contact and lead capture (`/contact`)

1. Build the “Let's Find Out What You Can Automate” hero and expectations copy.
2. Implement an accessible Inertia form containing:
   - full name;
   - business name;
   - work email;
   - phone/WhatsApp;
   - location;
   - document types;
   - approximate monthly volume;
   - current processing method;
   - biggest challenge;
   - preferred contact method; and
   - additional details.
3. Mark only genuinely required fields as required and show inline Laravel validation errors.
4. Disable duplicate submissions while processing and preserve entered values after validation failure.
5. On success, replace or reset the form and display the approved confirmation message.
6. Show direct email, telephone, and high-visibility WhatsApp actions below the form.
7. Explain what happens after submission and set a response-time expectation only if the business can meet it.

## 9. Demo Request Backend

### Persistence

Create a `demo_requests` table and `DemoRequest` model. Recommended fields:

- `id`;
- `full_name`;
- `business_name`;
- `work_email`;
- `phone` (nullable);
- `location` (nullable);
- `document_types` (text or JSON, depending on UI choice);
- `monthly_document_volume` (nullable normalized range/value);
- `current_process` (nullable text);
- `biggest_challenge` (nullable text);
- `preferred_contact_method` (nullable enum/string validated against an allowlist);
- `message` (nullable text);
- `status` (`new` by default, designed for later follow-up states);
- delivery timestamps/status fields if external dispatch needs auditing;
- timestamps.

Do not store uploaded business documents in the first public form. A real sample document can be requested later through a controlled channel after contact is established.

### Request handling

1. Add a dedicated `StoreDemoRequest` Form Request with explicit validation, normalization, maximum lengths, and friendly messages.
2. Add a single-action or resource controller that persists the lead before attempting external delivery.
3. Return an Inertia redirect with a flash success message to prevent browser resubmission.
4. Protect the endpoint with Laravel CSRF protection, rate limiting, and a honeypot or equivalent low-friction anti-spam measure.
5. Avoid logging full lead payloads or exposing submitted personal details in application errors.

### Lead delivery

1. Dispatch a queued notification/job only after the database transaction succeeds.
2. Email the configured business recipient with a concise lead summary and record identifier.
3. If an n8n/CRM webhook is required, send a signed or secret-authenticated server-side request with a strict timeout and retry/backoff policy.
4. A mail or webhook outage must not lose the lead; the database remains the source of truth.
5. Record delivery success/failure for operational follow-up and log only safe diagnostic context.
6. Configure a production queue worker and failed-job monitoring before launch.
7. Optionally send an acknowledgement email to the requester after the business recipient and wording are approved.

### Configuration

Add documented environment variables for public contact details and lead delivery, for example:

- `DOCUFLOW_CONTACT_EMAIL`
- `DOCUFLOW_PHONE`
- `DOCUFLOW_WHATSAPP_NUMBER`
- `DOCUFLOW_LEADS_EMAIL`
- `DOCUFLOW_LEADS_WEBHOOK_URL`
- `DOCUFLOW_LEADS_WEBHOOK_SECRET`

Expose only public contact values to page props. Secrets must remain server-side and must never be committed.

## 10. Trust, Accessibility, SEO, and Privacy

### Trust and privacy

- Add a short consent/privacy notice beside the demo form.
- Add Privacy and Terms pages if required for the deployment/assessment.
- State how submitted information will be used and provide a deletion/contact route.
- Define lead retention and access controls before launch.
- Review all claims against evidence and approved operational practices.

### Accessibility

- Use semantic landmarks and heading order.
- Ensure all controls are keyboard accessible with visible focus states.
- Give form fields persistent labels and associate errors with their fields.
- Meet WCAG AA contrast for text and interactive states.
- Provide meaningful alternative text and hide purely decorative graphics from assistive technology.
- Support reduced motion and avoid content that depends only on color.

### SEO and sharing

- Add unique title and description metadata to each page.
- Set canonical URLs after the production domain is known.
- Add Open Graph/Twitter metadata and an original DocuFlow social image.
- Add `Organization`/`ProfessionalService` structured data only with verified business facts.
- Add `robots.txt` and sitemap handling for public pages.
- Ensure authenticated and internal utility pages are not presented as marketing search results.

## 11. Testing and Quality Gates

### Backend feature tests

- Every public page returns successfully to guests.
- Named routes resolve correctly.
- Valid demo requests are persisted.
- Required fields and formats are rejected correctly.
- Oversized or invalid enum values are rejected.
- Successful requests dispatch the expected notification/job.
- Delivery failure does not remove the saved lead.
- Rate limiting and spam protection work.
- Authenticated dashboard/settings behavior remains unchanged.

### Frontend checks

- TypeScript passes with `npm run types:check`.
- ESLint passes with `npm run lint:check`.
- Prettier passes with `npm run format:check`.
- Production assets compile with `npm run build` without network font dependencies.
- Form loading, validation, error, and success states work.
- Navigation, mobile menu, FAQ, and all CTAs work with keyboard and touch input.

### Visual and browser review

- Review at 360px, 390px, 430px, tablet, laptop, and large desktop widths.
- Verify Chrome/Chromium and Firefox; include Safari/WebKit where available.
- Check light/dark behavior. The public site may use a deliberate light-first brand theme, but it must not inherit broken colors from the authenticated appearance setting.
- Check long content, zoom at 200%, slow connections, and disabled/reduced animation.
- Confirm no layout shift, missing icons, broken links, or horizontal overflow.

### Performance gate

- Keep the homepage lightweight and usable on slower East African mobile connections.
- Avoid unnecessarily large JavaScript bundles, background videos, and unoptimized images.
- Use responsive image dimensions and modern formats such as WebP or AVIF, with suitable fallbacks where needed.
- Lazy-load below-the-fold imagery while loading the above-the-fold hero deliberately to protect Largest Contentful Paint.
- Do not use autoplay video.
- Prevent avoidable layout shifts by reserving image and dynamic-content dimensions.
- Review the production bundle and remove unused dependencies or page code from the initial load where practical.
- Test the production build with mobile CPU and network throttling rather than judging performance only on localhost.
- Target strong Core Web Vitals—LCP, INP, and CLS—without sacrificing readability, accessibility, or conversion usability.
- Treat serious mobile performance regressions as release blockers.

### Full project gate

Run the existing project checks before handoff:

```bash
composer ci:check
npm run build
```

Also perform an end-to-end test using production-like mail/queue/webhook settings and confirm that a demo request reaches its real destination.

## 12. Delivery Sequence

Implement in this order so each stage leaves a reviewable result:

1. Audit and document the existing site, establish a passing regression baseline, and map the Business Projection/MVP facts to website content.
2. Approve brand basics, public contact values, and unresolved commercial values.
3. Create marketing routes, layout, design tokens, navigation, footer, and reusable components.
4. Build the Home and How It Works pages and approve the product narrative and visuals.
5. Build Pricing after final tier numbers and policies are supplied.
6. Build About and approve all research/founder claims.
7. Build Contact UI, demo-request persistence, notifications/webhook delivery, and spam controls.
8. Add metadata, privacy content, sitemap/robots behavior, and final brand assets.
9. Complete automated, responsive, accessibility, and cross-browser testing.
10. Configure production environment, queue worker, email/webhook delivery, logging, backups, and monitoring.
11. Run a real end-to-end lead test and retain submission/delivery evidence.
12. Complete a line-by-line 25-mark rubric audit and Business Projection/MVP consistency review before launch or grading.

## 13. Definition of Done

The website is ready when:

- all five pages are complete, original, responsive, and consistent;
- a new visitor can understand the service and next action within the homepage hero;
- every navigation and CTA destination works;
- all three packages show approved, transparent UGX pricing and allowances;
- research and security language is accurate and supportable;
- the demo form validates, saves, confirms, and reliably delivers leads;
- direct email, phone, and WhatsApp details are real and tested;
- the site contains no fabricated proof, placeholder commercial data, or remote build dependency;
- accessibility, mobile, SEO, privacy, and performance checks are complete;
- someone other than the developer has tested the live website on a real smartphone;
- after browsing without prior explanation, that tester can correctly state what DocuFlow does, who it serves, what it costs, and how to request a demo; if not, the affected content or navigation has been revised and retested;
- existing authentication and dashboard features still pass their tests; and
- `composer ci:check` and `npm run build` pass in a production-like environment.
