---
name: replica-launch
description: Prepare a differentiated landing page, evidence-based pricing, store metadata/listing checks, screenshots/checklists, and a launch plan after rebranding.
---
# Replica Launch
Read `replica/fixes.md`, `replica/brand.md`, `replica/brand.json`. Write `replica/launch/{landing.md,pricing.md,listing.json,launch-plan.md}`.

No fabricated proof/testimonials/user counts/ratings/press logos. Target-user reviews are research, not testimonials. Keep target trademarks out of app name/store keywords/ads unless qualified legal review supports a factual comparison.

Landing: hero angle, problem, three-step flow, differentiating fixes, pricing, FAQ, CTA, real product screenshot.
Pricing: current public competitor pricing with URL/date, evidence from feedback, simple model, max 3 tiers, clear cancellation/renewal behavior.
Store: fill listing JSON and run `python .agents/skills/replica-launch/listing.py replica/launch/listing.json`. Prepare current required screenshots/privacy/data-safety/support URLs/review notes by checking current store requirements at upload time.
Launch plan: beta/waitlist, analytics/error tracking, relevant communities, launch narrative around the fixes, first-user interviews.

Recommend `$replica-deploy`.
