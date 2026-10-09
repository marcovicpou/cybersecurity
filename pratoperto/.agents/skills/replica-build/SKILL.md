---
name: replica-build
description: Build a clean-room app rebuild screen by screen from recon, architecture, and design artifacts; implement every state, track feature parity, and save comparison screenshots.
---
# Replica Build
Read `replica/recon.md`, `replica/architecture.md`, and `replica/design/`. Update `replica/features.csv` and `replica/build-log.md`.

Write all implementation code fresh. Never paste target HTML/CSS/JS/SVG/assets/copy or load target CDN assets. Use tokenized styling.

1. Build routing/app shell/primitives and seed data.
2. Implement the core loop end-to-end first. If backend is not ready, use a fake data layer with stable interfaces.
3. For each screen: implement all observed plus empty/loading/error/permission/long-content/mobile states; semantic HTML; labels; keyboard; focus; alt text; responsive 390px and 1440px; no console errors.
4. Update matching feature rows to yes/partial with notes.
5. Save screenshots to `replica/clone-screens/Sxx.png` at reference viewports.
6. Keep `replica/build-log.md` with done/partial/missing notes.

If behavior is unclear, research lawful public docs or use the user's authorized account as a normal user; never inspect private implementation internals.

Finish with completion counts and recommend `$replica-backend` if data is fake, otherwise `$replica-test`.
