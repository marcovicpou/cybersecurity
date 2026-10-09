---
name: replica-test
description: Generate and execute a QA plan from recon flows, automate browser tests where possible, log reproducible bugs by severity, and fix S1/S2 with regression tests.
---
# Replica Test
Test the clone only, never fuzz/script/hammer the original.

Read flows from `replica/recon.md`. Write `replica/test-plan.md`, `replica/bugs.md`, and project E2E tests.

For each Fxx create happy, edge, and negative cases. Include empty/long/unicode input, concurrent tabs, double-submit, back/refresh, slow/offline, expired session, cross-user isolation, time zones/DST, mobile, keyboard, and labels.

Prefer Playwright with role/label selectors. Fail on console errors and 5xx; include accessibility checks when available. Manually verify things not safely automated, such as real email/OAuth/payment handoffs.

Bug severity: S1 security/data/payment/core-blocker; S2 feature broken no workaround; S3 workaround/visible defect; S4 cosmetic. Report only reproduced bugs with exact steps, expected, actual, evidence.

Fix S1/S2 first: failing regression test -> fix -> full suite. Ship with no open S1/S2.

Finish with run/pass/fail counts and recommend `$replica-diff`.
