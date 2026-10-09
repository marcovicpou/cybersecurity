---
name: replica-backend
description: Build auth, database migrations/access control, payments, email/jobs, and official third-party integrations for the rebuild using user-owned accounts and keys.
---
# Replica Backend
Read `replica/architecture.md`. Write migrations/server code, `.env.example`, and `replica/backend.md`.

Use only official public APIs with user-owned credentials. Never use the target app's private endpoints/OAuth clients. Never request or embed secrets in code; put variable names only in `.env.example`. Use test/sandbox modes first.

Auth: verification/reset, supported OAuth, secure httpOnly cookies, roles/teams authorization, account deletion.
Database: migrations, RLS or centralized authorization on every query, realistic fake seed data, backups.
Payments: hosted checkout/customer portal where appropriate, signature-verified idempotent webhooks, DB-backed subscription state, easy cancellation.
Email/jobs: user-owned sending domain/provider, fresh templates, retry/dead-letter behavior, UTC storage.
Integrations: least scopes, rate limits, provider review requirements.

Security gate: secrets in env; server validation; authz on reads/writes; auth/email/SMS rate limits; webhook verification; upload size/type limits; avoid PII in URLs/logs; dependency audit; privacy processor list.

Update features and recommend `$replica-test`.
