# Administration and account interface audit

## Scope boundaries

The Homzen redesign owns public Blade views and theme assets. The customer account area is rendered separately by `platform/plugins/real-estate/resources/views/themes/dashboard/layouts/master.blade.php`, while staff administration is rendered by Botble core. These are distinct authentication and permission contexts. No permission checks, guards, routes, database relationships, or production settings are changed by the visual redesign.

## Customer accounts

The existing `account_dashboard_header` filter loads a dedicated Homzen stylesheet (`editorial-account.css`) after the dashboard's existing styles. This keeps the visual integration in the theme and avoids copying core account layouts. It updates the navigation surface, headings, dashboard cards, forms, tables, tabs, pagination, and mobile drawer while preserving existing markup, asset initialization, DataTables, media-manager, alerts, and form behavior.

Existing theme authentication views remain wrappers around Botble's `FormAbstract` output: field names, CSRF tokens, validation errors, CAPTCHA, social login, password-reset handling, and registration settings continue to come from the original forms. Authentication modals retain their IDs and Bootstrap dismissal behavior. RTL account layouts still use Botble's original direction setting.

## Staff administration

Staff screens use `platform/core/base/resources/views`, with ACL, dashboard, media and table packages providing shared components. Real-estate administration views live under `platform/plugins/real-estate/resources/views`; their forms and tables depend on `FormAbstract`, core field components and existing JavaScript. Global public-theme CSS must not be injected into this area.

A matching admin refresh should be a separate, opt-in visual extension:

1. Register a dedicated stylesheet through Botble's supported admin asset hooks, scoped to an explicit staff-interface class.
2. Apply the same ink/cream surfaces, subdued RE/MAX accents, readable typography, consistent focus rings, and generous spacing to shared navigation, cards, forms and table chrome.
3. Keep workflow colors for danger, validation, success and pending states distinct and accessible. Do not hide controls to simulate authorization.
4. Review dashboard charts, account/property management tables, bulk actions, editors, media dialogs, nested menus, toast messages, modals and third-party plugin screens before enabling the extension.
5. Validate both English and Arabic, keyboard navigation, role-restricted menus, form errors and mobile layouts using authorized staff test accounts.

The staff admin audit is delivered here; no staff core templates have been restyled or modified. This prevents a public theme rollout from unexpectedly changing operational administration.

## Verification boundaries

Blade compilation and CSS build can validate templates and assets without logging in. End-to-end registration, login, password reset, authorization and dashboard editing require a configured local test account and any required email/CAPTCHA services. These interactions must be reported as untested until exercised. Public content, routes, shortcode configuration, social-share metadata, and post comment hooks are retained.

## Local visual validation performed

In an isolated preview database using a local QA account, Chromium successfully submitted the existing login form and navigated to `/account/dashboard`. The account stylesheet loaded through the extension hook. Dashboard layouts were inspected at 1440px and 390px with no horizontal overflow and no JavaScript page errors. Public login retained its CSRF token and original email/password/remember fields, rendered the split editorial layout, and fit both viewport sizes. These checks do not establish production credentials, email delivery, staff authorization, or write-workflow correctness.

The final published preview bundle was also exercised for multi-item property wishlists: a guest heart click opened the login modal without saving a cookie; the authenticated QA account saved two properties, removed the first without affecting the second, retained the saved state and badge after reload, and removed the final property to display the empty state. Guest registration and password-request pages returned HTTP 200 with their original CSRF-protected forms; neither was submitted. Account Properties and Settings pages returned HTTP 200, and the mobile account menu opened successfully. No production data or external email was involved.
