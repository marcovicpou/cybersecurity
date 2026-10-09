# Replica Skill Pack — OpenAI/Codex routing

Use the repo-local skills in `.agents/skills/` when the request matches their trigger.

Mandatory sequence for a full clean-room app rebuild:
1. `$replica-recon`
2. `$replica-architect`
3. `$replica-design`
4. `$replica-build`
5. `$replica-backend`
6. `$replica-test`
7. `$replica-diff`
8. `$replica-entrepreneur`
9. `$replica-brand`
10. `$replica-launch`
11. `$replica-deploy`

Rules:
- Rebuild functionality and UX patterns, not source code, private APIs, proprietary assets, trademarks, copy, licensed content, or user networks.
- Only inspect public sources and accounts/content the user is authorized to access.
- Never bypass paywalls, auth, rate limits, or terms restrictions.
- Write generated planning artifacts to `replica/` in the user's project.
- Prefer official public APIs and user-owned credentials.
- Before deployment, require tests green, must-have parity complete, brand sweep clean, and explicit user approval for going live.
- If only one stage is requested, run only that skill, but read existing `replica/` artifacts first.
