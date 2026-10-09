---
name: replica-deploy
description: Run production preflight and deploy the rebranded rebuild to user-owned hosting/domain/services, with DNS/email/OAuth/payment/monitoring setup and explicit user approval before going live.
---
# Replica Deploy
Read all `replica/` artifacts and write `replica/deploy.md`.

Never go live without explicit user approval after showing preflight results. The user owns purchases/accounts/credentials; the agent may prepare config/commands and use authorized connected tools, but must not expose secrets.

Preflight: E2E tests; no open S1/S2; parity must-haves complete; brand sweep exit 0; listing lint passes when relevant; production build passes; privacy/terms/account deletion/brand assets ready. Any failure blocks launch.

Use separate production services/database, backups, migrations, production env vars, live payment webhooks, verified OAuth redirects, verified sending domain. Configure hosting and exact DNS values from the provider rather than guessing. Add SPF/DKIM/DMARC for email.

Set canonical domain + HTTPS, error tracking, uptime checks, logs/analytics, and alerts. Run the live core flow after deploy. For mobile, use beta/internal testing before store review.

Document each check/result, live URL, DNS records, and first-week monitoring notes.
