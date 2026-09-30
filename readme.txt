=== WidgetCore ===
Contributors: mohamadjavadkarimi
Tags: elementor, faq, accordion, live search, comments
Requires at least: 6.7
Tested up to: 7.1.2
Requires PHP: 7.4
Stable tag: 0.0.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free Elementor widgets: an accessible FAQ accordion, an instant live search with a shortcode, and a comments list with form.

== Description ==

WidgetCore adds a new widget category to the Elementor panel with three widgets:

* **FAQ** (`wgcr-faq`) – accordion or toggle mode, plus / chevron / custom icon, H2–H4 or DIV question tags, automatic FAQPage JSON-LD schema, full ARIA (button + region), and complete style controls.
* **Live Search** (`wgcr-search`) – instant results while typing (REST API, debounced), keyboard navigation (Arrow Up/Down, Enter, Escape), combobox/listbox accessibility, thumbnails and excerpts, a "view all results" link, and multiple instances per page. Works for posts, courses or any public custom post type. Includes a `display_mode` option ("modal" dropdown under the input or "page" inline region inside the widget container), query filters (terms include/exclude, date range, order, sticky posts, query ID hook), pagination (numbers, previous/next, load more, infinite scroll), masonry / equal-height layouts and optional Elementor templates for each result.
* **Comments** (`wgcr-comments`) – the approved comments of the current post with replies, optional pagination and avatars, plus a configurable comment form: choose the fields, labels, required state, notes and layout, with a full Style tab. The form posts to WordPress' own comment handler, so moderation, spam checks and the login requirement from Settings → Discussion keep working. The widget can make name and email optional and can only make login stricter. It stores nothing of its own and loads no script.

The live search is also available as a shortcode:

`[wgcr_search source="post" placeholder="Search articles…" limit="5"]`

The comments list and form are available as a shortcode too:

`[wgcr_comments form="yes" list="yes" per_page="20"]`

The plugin has no settings page and stores no options. The widgets make no external requests of their own; the only server-side request is the update check against the GitHub API. If the Comments widget shows avatars, WordPress loads them from Gravatar in the visitor's browser; set the widget's avatar option to hide them.

= Translations =

The plugin is fully translatable (text domain `widgetcore`). Persian (fa_IR) and English (en_US) translations are bundled.

== Installation ==

1. Make sure Elementor is installed and active.
2. Upload the `widgetcore` folder to `/wp-content/plugins/`, or install the ZIP from Plugins → Add New → Upload Plugin.
3. Activate the plugin.
4. In the Elementor editor, find the widgets in the "WidgetCore" category.

== Frequently Asked Questions ==

= My courses use a different post type =

In the Query section choose "Custom post type" as the Source and enter the slug in the field that appears, or use the `source` shortcode attribute, e.g. `[wgcr_search source="sfwd-courses"]`. Only public, searchable post types are allowed; anything else falls back to posts.

= Which shortcode attributes are available? =

`source` (post type slug, default `post`), `placeholder`, `limit` (1–50, default `5`), `columns` (1–6, page mode only), `masonry` / `equal_height` (yes/no, page mode only), `thumb` (yes/no), `excerpt` (yes/no), `all` (text of the "view all" link), `template` (Elementor template ID, default `0`), `mode` (`modal` or `page`), `orderby` (`relevance`, `date`, `title`, `author`, `rand`, `menu_order`), `order` (`DESC`/`ASC`), `date` (`all`, `week`, `month`, `year`), `terms` (comma-separated `taxonomy:term_id`), `terms_op` (`exclude`/`include`), `ignore_sticky` (yes/no), `query_id`, `pagination` (`none`, `numbers`, `prev_next`, `load_on_click`, `load_on_scroll`), `spacer` (yes/no), `more_text`, `more_icon` (yes/no), `more_id` and `no_more` (custom "no more posts" message).

= Which shortcode attributes does [wgcr_comments] accept? =

Every Comments widget control has a matching attribute with the same id, for example `form`, `list`, `title`, `author`, `email`, `url`, `cookies`, `author_required`, `email_required`, `rows`, `submit`, `per_page` (0–100), `order` (`wp`, `asc`, `desc`), `avatar` (`wp`, `yes`, `no`), `avatar_size` (16–128), `reply`, `login_only` and `post` (a post ID). Invalid values fall back to the default. Only one comment form is printed per page.

= Does the plugin add a REST endpoint? =

Yes: `GET /wp-json/wgcr/v1/search?q=…&type=post&limit=5`. It is read-only, returns only published, non-password-protected content of public post types, requires at least 2 characters and returns at most 50 items per page. Optional parameters: `page` (adds `X-WP-Total` / `X-WP-TotalPages` headers), `orderby`, `order`, `date`, `terms`, `terms_op`, `ignore_sticky`, `query_id` and `template`. Developers can adjust the query with the `wgcr_search_query_args` filter (or `wgcr_search_query_args/{query_id}` for a single widget).

= How do updates work before the plugin is on WordPress.org? =

The plugin header declares `Update URI: https://github.com/WidgetCore/Plugin`, so WordPress core itself asks the plugin for updates (twice a day and whenever you open the Plugins or Updates screen). The plugin reads the latest GitHub release of `WidgetCore/Plugin` and, when its tag is newer than the installed version, WordPress shows the usual "update available" notice, the "View details" changelog and the native **Enable/Disable auto-updates** toggle. A "Check for updates" link on the Plugins screen forces a fresh check. Nothing is stored by the plugin; for a private repository or a higher API quota define `WGCR_GITHUB_TOKEN` in `wp-config.php`. Once the plugin is published on WordPress.org the header is removed and updates come from the directory.

= How are versions numbered? =

Every released ZIP increases the version by exactly one step: `0.0.1` → `0.0.2` … `0.0.9` → `0.1.0` → `0.1.1` … `0.9.9` → `1.0.0`. The changelog below lists every step.

== Changelog ==

= 0.0.8 =
* New Comments widget (`wgcr-comments`) and `[wgcr_comments]` shortcode: approved comments of the current post with replies, optional pagination and avatars, a configurable comment form (fields, labels, required state, notes, layout) and a full Style tab. Comments are sent to WordPress' own handler, so moderation, spam checks and login requirements keep working; the widget can make name and email optional and can only make login stricter. No option, table, cookie or script of its own.
* Comments: when the widget or shortcode prints for a post, the theme's own comment list and form (classic `comments_template()` and block-theme comments blocks) are no longer printed next to it.
* Comments: new Style controls — avatar width, avatar distance from the comment card, per-corner avatar radius, and the author name's and the date's distance from the avatar.
* Comments: a reply now lives inside its parent comment's card, and "Reply indent and spacing" is a four-side margin relative to that card. The logged-in user text has its own Messages controls (text with a `{name}` placeholder, profile and log-out links) and Style section. The avatar radius, distance and position are no longer overridden by theme rules such as `#comments .comment .avatar`.
* All widgets: every size control in the Style tab (widths, heights, padding, margins, gaps, radius, icon and avatar sizes) is responsive for desktop, tablet and mobile and has Normal and Hover tabs.
* Live Search: the shortcode and the widget read their defaults from one place.

= 0.0.7 =
* Updates straight from the GitHub repository (`Update URI` + WordPress core hooks, no library): latest release of `WidgetCore/Plugin`, release asset `widgetcore.zip`, "View details" changelog from the release notes, native per-plugin auto-update toggle, a "Check for updates" action link with a result notice, optional `WGCR_GITHUB_TOKEN` constant. Removing the header later switches updates to WordPress.org.

= 0.0.6 =
* Live Search: results rendered with an Elementor template no longer get the widget's card wrapper or styles — no nested links, no padding/hover/selection styles, and mouse hover never marks a result (keyboard navigation keeps an outline-only indicator). In Page display mode the results panel is unstyled too, so everything comes from the template.
* Live Search: Page-mode result lists grow with their results (the container max-height control scrolls the panel); infinite scroll observes the viewport.
* Release: a deterministic tests package (`widgetcore-tests.zip`, the `test/` folder without Markdown files) is built and hash-checked next to the source and documents packages; the release table links download directly. Every published ZIP now carries its own version number, so browsers never reuse cached `search.css`/`search.js` from a previous build.

= 0.0.5 =
* Versioning: the plugin now uses a per-release counter (`0.0.1` → `0.0.2` … `0.0.9` → `0.1.0` … `0.9.9` → `1.0.0`); earlier entries below were renumbered (0.1.0 → 0.0.2, 0.2.0 → 0.0.3, 0.2.1 → 0.0.4).
* Live Search: the results count is now driven by "Items per page" in the Layout section (1–50, default 12); the "Results count" control in General was removed. Widgets that saved the old value keep it until "Items per page" is set once.
* Live Search: the "Columns" control now works — in Page display mode the results render as a responsive CSS grid (desktop/tablet/mobile values, defaults 3/2/1). List mode stays single-column and hides the control.
* Live Search: removed the placeholder "Apply an alternate template" and "Template type" controls from Layout.
* Live Search: "Custom post type" moved out of the Query list into the Source select — pick "Custom post type" there and the slug field appears. The slug no longer overrides an explicitly selected source.
* Live Search: the Page-mode "Results container" style controls now live inside the "Results panel" section and are shown only in Page display mode; the horizontal alignment control no longer overrides the margin control.
* Live Search: the Query controls are now applied — include/exclude terms from any public taxonomy, date range, order by (relevance, date, title, author, random, menu order) and order, sticky posts first, and a Query ID that feeds the `wgcr_search_query_args/{query_id}` filter. Matching REST parameters and shortcode attributes were added.
* Live Search: the Pagination controls are now applied — page numbers, previous/next, "load more" button (text, icon, ID) and infinite scroll inside the results panel, with alignment, spacer and a custom "no more posts" message. The REST endpoint returns `X-WP-Total` / `X-WP-TotalPages` headers when `page` is requested.
* Live Search: "Masonry" and "Equal height" now work in Page display mode.
* Live Search: template `<link>` stylesheets are loaded as real stylesheets (once per page) instead of being inlined as text; "view all" arrow, hover shift and selection bar now follow the text direction; motion is reduced for `prefers-reduced-motion`.
* Live Search: template and term option lists are only queried inside the Elementor editor, not on every frontend render.
* Live Search: shortcode `limit` accepts 1–50 and a new `columns` attribute (page mode); REST `limit` cap raised to 50 (`WGCR_SEARCH_MAX_RESULTS`).
* FAQ: closed answers are removed from the tab order (visibility toggle), Arrow Up/Down, Home and End move between questions, inline editing works in the editor preview, reduced-motion support, and the widget file follows the WordPress coding style.
* Shared SVG icons (`wgcr_svg_icon()`) replace the duplicated icon helpers of both widgets.
* Verified against WordPress 7.1.2 and Elementor 4.3.2 (the official release tag) plus WordPress 6.7.4 with Elementor 4.0.8; the test matrix now covers Elementor's generated responsive CSS for the results columns.

= 0.0.4 =
* Live Search: Elementor template results now carry the template's own CSS (inlined once per response), so custom templates render styled in both display modes.
* Live Search: injected template results are initialized with Elementor's frontend handlers and `elementor-invisible` entrance-animation placeholders are resolved, so animated templates no longer show as empty.
* Live Search: a template that renders without visible content now falls back to the default result card instead of returning empty items.
* Live Search: fixed the results-container width and alignment controls — both now apply to the results box itself instead of the whole widget, with the field gap preserved in Page mode.

= 0.0.3 =
* Live Search widget: rebuilt content settings into General (display mode first), Layout, Query and Pagination sections, matching Elementor's own loop widgets. Layout adds a template-type selector with custom post type support, a searchable Elementor template picker with an edit link, columns, items per page, masonry, equal height and an alternate template. Query adds include/exclude terms, date range, orderby/order, sticky control and a query ID. Pagination adds type, alignment, load-more button and custom messages. Style adds a results-container section (width/height and more) for Page mode.
* Live Search widget: fixed Page display mode (results container styling) and hardened REST template rendering so a broken Elementor template can no longer fail the whole search request.

= 0.0.2 =
* Live Search widget: added a `display_mode` option (Modal / Page). When set to Page, results render in a permanently visible region inside the widget container instead of the dropdown panel. Shortcode `[wgcr_search]` now accepts a `mode="page"` attribute.

= 0.0.1 =
* Initial release: FAQ widget (`wgcr-faq`) with FAQPage schema, and Live Search widget (`wgcr-search`) with the `[wgcr_search]` shortcode.
* Full i18n support with bundled fa_IR and en_US translations.
* WordPress.org Plugin Check compliance (late escaping, no heredoc, prefixed globals).
* Bundled license: GPL-2.0-or-later (LICENSE).
* Verified against the latest WordPress and Elementor releases before every release build.
