---
name: replica-architect
description: Turn replica recon artifacts into a practical stack, SQL schema, API/server-action map, integration plan, risk notes, and build order. Use after replica-recon or when planning the rebuild architecture.
---
# Replica Architect
Read `replica/recon.md` and `replica/features.csv`. Write `replica/architecture.md` and `replica/schema.sql` or migrations.

Prefer the user's existing stack. Otherwise choose boring managed technology; one database; no premature microservices. Explain each choice briefly.

For schema design include UUID IDs, timestamps, owner/org columns, explicit foreign-key delete rules, indexes on joins/filter/sort columns, constrained statuses, UTC timestamptz, integer money + currency, and an authorization/RLS strategy. Encode race-condition invariants in the database when possible.

Map every flow to routes/server actions: method/path, purpose, caller, input, output, flow. Include official webhooks/jobs and only official public APIs with user-owned credentials.

Cover applicable risks: time zones/DST, idempotency, races, rate limits, uploads, search, realtime, offline, multi-tenancy, deletion/privacy.

Build order: core vertical slice first, then must, should, could, then evidence-backed differentiators. Each milestone references S-IDs, tables, and routes.

Finish with stack summary, table/route counts, top risks, and recommend `$replica-design`.
