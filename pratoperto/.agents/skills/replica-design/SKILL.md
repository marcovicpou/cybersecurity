---
name: replica-design
description: Reconstruct the target product's design system as neutral design tokens and accessible component specs using original/open assets. Use after architecture or when matching UX structure without copying brand assets.
---
# Replica Design
Read recon/screens. Write `replica/design/tokens.json`, `tokens.css`, framework mapping, and `components.md`.

Rebuild system roles and behavior, not proprietary assets or brand identity. Never copy logos, illustrations, photos, sounds, proprietary icons/fonts, or original copy. Use open icons/fonts and fresh wording. Treat target brand colors only as observations; final branding must be distinct.

Measure color roles, typography scale, spacing, radii, shadows, motion, layout widths/breakpoints/sidebar/header. Use semantic token roles rather than raw values in components.

Specify each component's variants, sizes, states, tokens, accessibility semantics, keyboard behavior, and screens used. Include focus, disabled, loading, error, and empty states.

Build primitives once before screens. Run:
`python .agents/skills/replica-design/contrast.py replica/design/tokens.json`
Require WCAG AA pass for configured pairs.

Finish by recommending `$replica-build`.
