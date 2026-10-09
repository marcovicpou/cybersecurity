---
name: replica-brand
description: Rebrand the rebuild with a distinct name, palette, voice, logo brief, availability checks to run, and a codebase sweep for target-brand leftovers. Must run before launch.
---
# Replica Brand
Read the positioning from `replica/fixes.md`. Write `replica/brand.md` and `replica/brand.json`.

Generate varied name candidates, reject confusing similarity, then document trademark/domain/store/handle/web checks with date/result or `to run`; never claim availability without checking. Screening is not legal clearance.

Create a palette from a distinct color family and update semantic tokens. Run contrast check to zero AA failures.

Create a logo brief only: concept, mark type, 16px/1024px requirements, SVG/app icon/favicon/social deliverables, and an explicit non-similarity check against the target mark.

Define brand voice and rewrite high-frequency UI strings freshly.

Run `python .agents/skills/replica-brand/sweep.py . --config replica/brand.json`. Fix until exit code 0. Also visually inspect favicon/titles/emails/OG/app icon.

Recommend `$replica-launch`.
