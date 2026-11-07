# RE/MAX Professional editorial redesign: architecture and design audit

## CMS homepage rendering

The live homepage must remain a CMS page, not a static template. `Botble\Theme\Http\Controllers\PublicController::getIndex()` resolves `BaseHelper::getHomepageId()` through `PageService::handleFrontRoutes()`. That service retrieves the published Page record and its metadata, applies its selected layout, and renders the theme's `views/page.blade.php`. `config.php` registers `withShortcodes()` for page/post/property/project views, so the CMS page's existing shortcode text expands through theme registrations. Page content, ordering, media selections, translated versions, SEO descriptions and shortcode options remain administrator-editable.

`views/index.blade.php` is the fallback setup instructions screen used when no homepage is assigned. It is not the live homepage and is not a replacement target for production content.

The homepage commonly combines `hero-banner`, `properties`, `featured-projects`/`projects`, `location`, `services`, `about-us`, `agents`, `blog-posts`, `testimonials` and `call-to-action`. Each registered shortcode delegates to `partials/shortcodes/<name>/index.blade.php`, usually dispatching a selected `styles/style-*.blade.php`. Preserve all configured variants rather than assuming style 1 exclusively.

## Component and dependency map

| Surface | Existing entry points | Dependencies retained |
| --- | --- | --- |
| Shared document | `layouts/base.blade.php` | Theme header/footer asset hooks, SEO metadata, body attributes, filters, fonts and language direction |
| Public frame | `layouts/default.blade.php`, `layouts/full-width.blade.php` | Ads/hooks, top header, header, breadcrumbs, CMS content and footer widget sidebars |
| Navigation/footer | `partials/header.blade.php`, `main-menu`, `top-header`, `footer` | CMS menus, translated links, currency/language switching, authentication, wishlist count and editable footer sidebars |
| Homepage hero/search | `partials/shortcodes/hero-banner/**`, `search-box/**` | Configured images/copy, sale/rent state, existing search names/actions, sliders and autocomplete hooks |
| Listing shortcodes | `functions/shortcodes-real-estate.php`, `src/Actions/GetPropertiesAction.php`, `GetProjectsAction.php` | Published/featured/category query rules, limits and filters, dynamic models and property/project item partials |
| Location shortcodes | `functions/shortcodes-location.php`, `partials/shortcodes/location/**` | Configured location IDs and actual listing counts/media, destination URL generation |
| Property/project collections | `views/real-estate/properties.blade.php`, `projects.blade.php`, `listing-layouts/**`, `properties/**`, `projects/**` | Existing plugin controller queries, GET filters, sorting, pagination, AJAX fragments, grid/list/map layouts and wishlist data attributes |
| Property/project details | `views/real-estate/property.blade.php`, `project.blade.php`, `single-layouts/**`, `partials/**` | Slugs, price visibility, galleries/Fancybox, maps/Leaflet, features, reviews, contact/consult forms, related listings and schema metadata |
| Agents | `views/real-estate/agents.blade.php`, `agent.blade.php` | Existing account records/profile bindings, listing relationships, forms and pagination |
| Blog/editorial | `views/category.blade.php`, `tag.blade.php`, `search.blade.php`, `loop.blade.php`, `post.blade.php`, `blog/partials/posts` | Published records, article metadata, translated URLs and CMS content |
| Information/contact/FAQ | `views/page.blade.php` plus contact/FAQ/about/services shortcode partials | Dynamic page/shortcode content and existing form/validation/inquiry behavior |
| Authentication | `views/real-estate/account/auth/**`, `partials/modal-authentication.blade.php` | Account guard, existing forms, password reset, CSRF and authorization routes |
| Account dashboard | Real Estate plugin account layout/view tree | Plugin navigation, tables/forms, permissions and workflows; theme-owned stylesheet through `account_dashboard_header` hook |
| Admin | Botble core admin layouts and modules | Separate audit/refresh plan in `docs/redesign-admin-audit.md`; no core admin redesign mixed into the public theme |

`routes/web.php` within the theme supplies wishlist and `/ajax/...` property/project/city endpoints, then delegates to `Theme::routes()`. Real Estate plugin `routes/fronts.php` supplies public listings, city/state collections, agent and account routes. No route names, slugs or query contracts need to change for this presentation work.

## Assets and presentation boundaries

The root Mix entry discovers theme `webpack.mix.js` files. Homzen compiles `assets/sass/style.scss` and `assets/js/script.js` to `public/themes/homzen`; `config.php` registers those assets in the public document. The production Mix path copies the compiled assets to the theme's committed `public` distribution. Account dashboard styles use a separate compilation target and opt-in hook, keeping admin CSS isolated.

New editorial partials supplement the existing component styles. English editorial headlines can use system serif accents, with existing self-hosted brand body fonts. Arabic retains the configured Arabic font stack and logical reading direction. CSS uses logical properties, responsive grids and reduced-motion alternatives. Existing jQuery, Bootstrap, Swiper, Fancybox, Leaflet and filter scripts remain in place; no framework migration is required.

Keep legacy selectors/data attributes used by AJAX, wishlist counts, map popups, search tabs and gallery initialization intact. New decorative wrappers must not become nested links, hide functional controls or block keyboard access.

## Reference inspection

The Caldren reference was inspected in Chromium using the environment's existing proxy CA trust with TLS verification enabled. Screenshots and extracted page metadata were saved outside the repository under `/workspace/professional-redesign-artifacts/` for homepage, properties, developments, locations, a property detail and mobile layouts. Pages were scrolled before screenshots to trigger reveal animations.

Observed desktop design principles:

- A sparse horizontal navigation with small type, a fine rule and a compact active pill.
- Oversized editorial headings contrasting clean sans-serif with italic serif phrases, substantial whitespace and fine utility labels.
- A homepage architectural image composition and blue atmosphere; the RE/MAX implementation uses its own media/branding instead of copying that composition or source assets.
- Property collections use a light, restrained horizontal filter strip and three-column imagery. Property names/prices sit below images rather than inside ornamental cards.
- Development features prioritize very large imagery with small overlaid status and title elements.
- Property details prioritize title/location/price followed by an asymmetric image gallery: a large image beside four smaller images.
- Location pages combine a numbered horizontal navigation rule with asymmetric photography and editorial text.

The implementation is an original RE/MAX system using warm light surfaces, ink text, strong architectural media and restrained brand red/blue. Reference listings, prices, market figures, testimonials, imagery and source code must never become production content.

## Data and release protections

Production data, schema, relationships, models, media references, auth guards and permissions remain unchanged. New content labels are translatable; meaningful content still comes from current models/CMS configuration. SEO stays in existing theme/service hooks, including canonical tags, hreflang, structured data and sitemap behavior.

Validation must distinguish empty local content from production data availability. Local visual fixtures, if used, belong only in a disposable local/testing context and must not be committed as real listings or applied to production. The change is reviewed on `redesign/caldren-inspired`; merge and deployment remain separate user actions.
