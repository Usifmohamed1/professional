# Editorial redesign: implementation and validation

## Design and implementation

The Caldren reference was visually inspected in Chromium on desktop and mobile: homepage, properties, developments, locations and a residence detail. The implementation uses an original RE/MAX composition: warm paper and dark ink surfaces, large English serif headings, local Lama Sans Arabic typography, architectural imagery supplied by the CMS, image-first borderless cards, a bottom-docked hero search and restrained red/blue accents. No reference branding, images or source were copied.

The shared system is in `platform/themes/homzen/assets/sass/_editorial.scss`, with separate home, estate and public-page layers imported by `style.scss`. `editorial-account.scss` is independently compiled and injected through `account_dashboard_header`; staff Botble administration is audited separately in `redesign-admin-audit.md`.

Template changes cover shared navigation/footer/breadcrumbs; existing hero, property/project, about, services, location, agent, journal and supporting shortcode variants; property/project grid/list/detail/gallery; agent and wishlist screens; public journal/articles/information pages; authentication; and account dashboard styling. The existing CMS page content, shortcode parameters, models, routes, form objects and JS event/data contracts are retained. No migrations, dependency changes, production data edits or deployment are part of this change.

Accessibility improvements include an actual content skip target, keyboard-operable navigation with focus containment/Escape restoration, labelled unique search inputs, non-nested wishlist buttons with pressed state, gallery labels, reduced-motion behavior, and Arabic translations for new interface text. JavaScript location widgets are initialized per form rather than through one duplicated ID. Wishlist IDs are normalized before cookie removal.

## Build and run

From the existing checkout (no worktree required):

```sh
npm_config_theme=homzen npm run prod
php artisan cms:theme:assets:publish homzen
php artisan view:cache
php vendor/bin/phpunit
```

The production Mix target updates the theme's committed distributable CSS/JS and publishes the account stylesheet. The development target writes to `public/themes/homzen`; Botble's configured public alias is `remax-professional`, so use the publish command to refresh served theme assets. Public CSS/JS versions include the built stylesheet timestamp to refresh browser caches. Do not apply onboarding's old instruction to avoid production mode when deliberately rebuilding theme code: its source-protection rule was specific to setup.

In this cloud environment PHP/Composer run through `/workspace/professional-env/php` and `/workspace/professional-env/composer`. No deployment was performed.

## Executed checks

| Check | Result |
| --- | --- |
| Theme development and production Mix build | Passed; Sass deprecation warnings remain |
| Blade view compilation | Passed |
| Existing PHPUnit suite | Passed: 2 tests, 2 assertions |
| Browser smoke script, 1440px and 390px | Passed: home, listings, login; one h1, no horizontal overflow, CSRF retained |
| Additional responsive widths | Homepage verified without overflow at 320px, 768px and 1024px |
| Browser mobile menu | Passed: open state, Escape close, trigger focus restoration |
| Reduced motion | Passed: readable hero and no pending reveal content |
| JavaScript | No uncaught page errors during tested flows |
| Search | Sale/rent, keyword, city, category, price, empty state, sort and pagination responses verified |
| Property/project details and public pages | HTTP 200 on populated local fixtures |
| Arabic | Genuine `/ar` route verified RTL at 390px; Arabic listing at 1440px; Lama Sans headings, no horizontal overflow |
| SEO presentation | Canonical/hreflang output observed on configured homepage; existing SEO rendering calls retained; no production crawl performed |
| Map tiles | Tested configured map with existing trusted browser CA: 10/10 tiles loaded; TLS verification kept enabled |
| Inquiry form | Invalid request returned field errors (422); local-only valid request succeeded with array mailer |
| Wishlist | Passed: guest login modal; authenticated two-item save/remove, badge, pressed state, reload persistence and empty state |
| Account | Original login succeeded; dashboard, Properties and Settings returned 200; mobile account drawer and responsive layout verified |
| Registration/password request | HTTP 200, original email fields and CSRF present; no submissions |
| Media | Existing public user-site images used only in ignored local QA fixtures; no media committed as application content |

The reproducible `tests/Browser/editorial-smoke.cjs` uses the existing Puppeteer dependency and Chromium. It only permits a loopback base URL and blocks mutations except the existing read-only listing search POST. Run against an already configured CMS preview:

```sh
BASE_URL=http://127.0.0.1:8001 node tests/Browser/editorial-smoke.cjs
```

Optional `CHROMIUM_PATH`, `HOME_PATH`, `LISTING_PATH`, `LOGIN_PATH`, `SEARCH_TERM`, and `SCREENSHOT_DIR` configure the executable, paths, search and screenshots. It does not create fixtures or alter application records.

## Data isolation and remaining review

Visual and functional checks used a separate `redesign_preview` database with explicitly labelled QA fixtures. The normal local `professional` and `testing` databases were preserved. Preview environment files, credentials, SQL, media and fixture builder are ignored/outside the checkout, and are not part of the PR. Local inquiry mail used the array transport. Screenshots under `docs/screenshots` depict this QA preview, not production listings.

Registration, email delivery/password-reset completion, CAPTCHA/social login, account edits/property publishing, all third-party map tile providers, all gallery/map layout combinations and a production SEO/performance crawl still require environment-specific review. Staff administration has a matching refresh plan, not a public-theme CSS injection. Existing test coverage is small, so the PHP suite alone does not establish complete behavioral coverage. Review on a content-rich staging site before release; this branch is neither merged nor deployed.
