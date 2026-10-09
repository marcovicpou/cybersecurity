---
name: replica-diff
description: Score feature parity and compare reference/clone screenshots structurally. Use after testing or whenever the user asks how close the rebuild is and what remains.
---
# Replica Diff
Parity means same job, not same branding/pixels.

Run:
`python .agents/skills/replica-diff/parity.py replica/features.csv`
`python .agents/skills/replica-diff/imgdiff.py replica/screens/S07.png replica/clone-screens/S07.png --out replica/diffs/S07.png --json`

Feature weights: must=3, should=2, could=1; partial=0.5; skip and clone-only extras excluded. Missing must-haves block shipping.

Compare screenshots at the same viewport/state. Default layout mode focuses on luminance/edge structure and ignores most color differences. Pixel mode is for clone-vs-clone regressions, not copying target trade dress.

Also compare behavior manually: steps/clicks, errors, persistence, notifications/emails. Fewer steps may be an improvement.

Write `replica/parity.md`: feature score, screen scores, missing build order, behavior differences, verdict. Shippable requires all must-haves and no open S1/S2; recommend gaps or `$replica-entrepreneur`.
