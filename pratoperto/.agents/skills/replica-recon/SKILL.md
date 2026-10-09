---
name: replica-recon
description: Reverse-engineer an app from public pages, screenshots, docs, store listings, walkthroughs, and the user's own authorized account into screens, flows, components, inferred data model, and a feature matrix. Use first for clean-room rebuilds.
---
# Replica Recon
Create `replica/recon.md`, `replica/features.csv`, and optionally `replica/screens/`.

Rules: public sources and user-authorized access only; no source-code copying, bundle decompilation, private endpoint harvesting, credential collection, paywall bypass, or bulk scraping. Screenshots are reference-only and never ship.

Workflow:
1. Define target app/platform, exact slice, target users, and core loop. If unspecified, choose a narrow core loop and state the assumption.
2. Build a source table with URLs: docs/help, pricing, changelog, store listing, public walkthroughs, marketing, public API docs, and authorized account observations.
3. Inventory screens as S01... with route/entry, purpose, components, and states including empty/loading/error/permission/mobile.
4. Map flows as F01... with happy path, screen sequence, click count, and edge cases.
5. Inventory repeated components and states.
6. Infer entities/fields/relationships with evidence and confidence; mark guesses explicitly.
7. Populate `replica/features.csv`: feature, area, priority (must/should/could), original, clone, notes. Start clone=no.
8. Mark uncloneable/licensed/network/data-owned items as skip with reason.
9. Size the project S/M/L/XL and identify hard parts.

Finish with the core loop, screen/flow counts, three hardest parts, out-of-scope items, and recommend `$replica-architect`.
