---
name: replica-entrepreneur
description: Research real public user feedback about the target product, rank complaints/requests/unsolved jobs, create a fix plan, and derive differentiated positioning. Never fabricate reviews.
---
# Replica Entrepreneur
A straight copy has no reason to exist; use evidence to make the rebuild better.

Collect public, attributable reviews/threads into `replica/reviews.csv` with `source,url,date,rating,text`. Prefer diverse sources and recent evidence. Use normal browsing or official feeds/APIs under their terms; do not scrape prohibited surfaces.

Never invent reviews, quotes, counts, ratings, or users. Keep short quotes verbatim and linked for research only; never reuse target reviewers as your testimonials.

Run `python .agents/skills/replica-entrepreneur/reviews.py replica/reviews.csv --out replica/feedback.md`.

Produce three evidence-ranked lists: what users hate, what is missing, what remains unsolved. Mark thin evidence. Pick 5–8 fixes by evidence × implementation cost, add clone-only rows to `features.csv`, and write `replica/fixes.md`.

Create three positioning options grounded in evidence and recommend one. Do not use the target trademark in your product name.

Finish with sample size/source count and recommend `$replica-brand`.
