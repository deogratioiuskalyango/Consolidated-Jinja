# Master Prompt: Role-Specific Dashboards and Workspaces

You are working on the Jinja Consolidated Property / Governance Laravel application. The current multi-role system assigns secondary roles such as Director, Secretary, Auditor, Finance Manager, Landlord, Tenant Manager, and Compliance Officer, but those roles must not be cosmetic aliases of the owner dashboard.

## Goal
Build a real role workspace system where each role has its own dashboard, navigation, workflows, metrics, actions, and permissions. A role dashboard must feel and behave like a purpose-built workspace for that role, not a copied owner dashboard with a different title.

## Non-Negotiable Requirements
1. Do not route secondary roles to `owner.dashboard` unless the role is literally `owner`.
2. Each role must have a distinct route, route name, dashboard title, quick actions, workflow panels, metrics, empty states, and role-appropriate navigation.
3. Every visible button/link must go to a real route or submit a real form. No `href="#"` placeholders.
4. Every dashboard must be responsive across mobile, tablet, desktop, and wide desktop.
5. Every dashboard must use the shared theme variables for text, surfaces, borders, cards, and focus states.
6. Tables, panels, modals, and long content must scroll within their containers without clipping content.
7. Role switching must send the user to that role's dashboard route through `SystemUserRole` metadata.
8. The owner portal may provide underlying workflow routes, but the dashboard and navigation shell must be role-specific.
9. Add safe fallbacks and empty states so dashboards do not break when data is missing.
10. Prefer shared dashboard components/configuration for consistency, but keep role content distinct.

## Role Workspaces
- Director: executive KPIs, board meetings, financial approvals, resolutions, strategic reports.
- Finance Manager: revenue, expenses, invoices, rent collections, reports, pending expenses, reconciliation.
- Landlord: properties, units, occupancy, rent income, maintenance exposure, recent payments.
- Tenant Manager: tenant onboarding, active tenants, vacancies, expiring leases, tickets, notices.
- Secretary: meetings, minutes, governance documents, resolutions, shareholder communications.
- Auditor: audit logs, financial anomalies, large transactions, compliance trail, reports.
- Compliance Officer: governance documents, resolutions, policy risks, missing documents, compliance alerts.

## Implementation Pattern
- Add dedicated role workspace routes such as `role.director.dashboard`, `role.finance-manager.dashboard`, etc.
- Add middleware that verifies the authenticated user has the active role assigned and active in `system_user_roles`.
- Update `SystemUserRole::allRoleSlugs()` so each secondary role points to its own route.
- Add a `RoleWorkspaceController` that prepares dashboard data per role and a consistent config payload for UI rendering.
- Add a role workspace layout separate from `owner.layouts.app` with role-specific sidebar/actions.
- Use real existing workflow routes for actions where possible. Hide or disable unavailable actions rather than linking to `#`.
- Keep owner workflow pages accessible only where appropriate, but do not visually present them as the role dashboard shell.

## Verification
- Run PHP lint on all changed PHP files.
- Run `php artisan route:list` for all role dashboard routes.
- Compile Blade views with `php artisan view:cache`.
- Smoke test each role dashboard controller path.
- Verify no `href="#"` remains in role dashboard views.
- Check mobile and desktop layout behavior.
