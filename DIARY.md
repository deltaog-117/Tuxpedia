# 📘 DIARY.md

## Architectural Decision Log

*A chronological record of decisions, trade-offs and reasoning. Git tracks what changed; this tracks why.*

---

## 📋 Decision Index

| Date | Decision Area | Choice | Status |
|------|---------------|--------|--------|
| 2026-09-19 | UI direction | Modern Docs (COA 2) | ✅ Confirmed |
| 2026-09-19 | Markdown safety | Strip HTML in `Content::toHtml()` | ✅ Confirmed |
| 2026-10-23 | Page-to-distro link | Explicit `distribution_id` column (COA B) | ✅ Confirmed |
| 2026-10-23 | Wiki content delivery | Markdown files in the repo plus an idempotent seeder (COA B) | ✅ Confirmed |
| 2026-10-23 | Distro logo delivery | Bundled SVGs painted via CSS mask, initial as fallback | ✅ Confirmed |
| 2026-10-24 | Home page layout | Encyclopedia masthead replaces the gradient hero | ✅ Confirmed |
| 2026-10-24 | Family tree parents | Resolve `based_on` by normalised name in `BuildFamilyTree` | ✅ Confirmed |
| 2026-10-24 | Terminal simulator | Client-side shell with pipes, redirects and Tab completion | ✅ Confirmed |
| 2026-10-25 | Command palette | Livewire modal reusing the search stack (COA A) | ✅ Confirmed |
| 2026-10-25 | Revision history | Git holds content, slim `wiki_revisions` rows point at commits (COA C) | ✅ Confirmed |
| 2026-10-25 | Discovery pages | Single-purpose Actions + `DiscoveryController`, plain Blade views (COA A) | ✅ Confirmed |
| 2026-10-25 | Wiki links | CommonMark inline parser + `wiki_links` table synced by a `Page` observer (COA C) | ✅ Confirmed |
| 2026-10-25 | Citations | CommonMark `FootnoteExtension`; References heading drawn in CSS | ✅ Confirmed |
| 2026-10-25 | Categories | Relational `wiki_categories` + pivot, comma-separated editor field (COA A) | ✅ Confirmed |
| 2026-10-26 | Article cross-links | Hand-placed `[[slug\|label]]` at first prose mention, guarded by a test | ✅ Confirmed |
| 2026-10-26 | Per-distro landing pages | Dedicated `/distro/{slug}` page with text lineage, siblings and derivatives (COA B) | ✅ Confirmed |
| 2026-10-26 | Roles and edit permissions | One `role` enum column plus `PagePolicy` (COA A), editing requires an editor | ✅ Confirmed |

---

## 📝 Decision Entries

### UI Redesign Direction

**Date:** 2026-09-19
**Author:** deltaog-117
**Status:** Confirmed

#### Context / Background

The v0.1.0 UI was a plain GitHub-light theme: white navbar, grey background, thin-bordered cards, default blue buttons, no dark mode and no visual identity. The goal is a more portfolio-worthy look without new build tooling (CSS is compiled with the Tailwind CLI, not Vite).

#### Options Considered

**Option A: Terminal Native (dark hacker aesthetic)**

| Aspect | Assessment |
|--------|------------|
| **Advantages** | Unmistakably Linux; terminal and D3 tree already fit; one palette covers everything |
| **Disadvantages** | Most common Linux-site look; long articles are harder to read light-on-dark; neon needs accessibility care |
| **Implementation Difficulty** | Low–Medium |

**Option B: Modern Docs (Linear/Vercel-style polish)**

| Aspect | Assessment |
|--------|------------|
| **Advantages** | Reads as a professional product; good for long-form content; dark mode is a real portfolio feature |
| **Disadvantages** | Every component needs a dark variant; ToC and theme-toggle JS; can look generic |
| **Implementation Difficulty** | Medium–High |

**Option C: Distro Identity (colour-coded, data-rich)**

| Aspect | Assessment |
|--------|------------|
| **Advantages** | Most distinctive; makes comparison and tree richer |
| **Disadvantages** | Touches data and Blade, not just styles; needs a colour per distro; brand colours can clash |
| **Implementation Difficulty** | High |

#### Decision

**Option B.** It gives the best return for a reviewer who spends seconds on the home page, and it is the strongest base for layering Option C's per-distro accents later.

#### Implementation Notes

- Colours are CSS variables holding RGB channels (`--tux-*`), wrapped in `tailwind.config.js`, so Tailwind opacity modifiers (`bg-tux-surface/80`) work and dark mode is a single class swap on `<html>`.
- Existing token names were kept; `tux-blue` now aliases the indigo accent to avoid a rename across the code base.
- Component classes follow BEM (`.hero__title`, `.site-nav__link--active`) per the CSS etiquette template. The old `.card`, `.btn-*`, `.badge-*` and `.input-field` names were preserved.
- The D3 tree is styled with CSS classes instead of inline `fill`/`stroke`, so a theme toggle recolours it without redrawing.
- The table of contents is built client-side from the rendered headings, which avoids a PHP change; it is a progressive enhancement and hidden with fewer than two headings.

#### Follow-ups

- Sanitise Markdown output (see ROADMAP).
- Option C distro accents.

---

### Safe Markdown Rendering

**Date:** 2026-09-19
**Author:** deltaog-117
**Status:** Confirmed

#### Context / Background

`wiki/show` printed `Str::markdown($page->content)` with `{!! !!}`. The installed Laravel version passes no options to CommonMark, whose defaults allow raw HTML and unsafe link schemes. Any user who can create or edit a page could inject a script that runs for every visitor (stored XSS).

#### Options Considered

**Option A: Pass strict options to `Str::markdown()` in the view**

| Aspect | Assessment |
|--------|------------|
| **Advantages** | One-line change; no new dependency |
| **Disadvantages** | Security settings live in a template; easy to forget if another view renders content; hard to unit test |
| **Implementation Difficulty** | Easy |

**Option B: Put rendering on the `Content` value object (`toHtml()`)**

| Aspect | Assessment |
|--------|------------|
| **Advantages** | One place owns the safe settings; views cannot pick unsafe ones by accident; plain unit tests without a database |
| **Disadvantages** | Slightly more code than a one-liner |
| **Implementation Difficulty** | Easy |

**Option C: Render freely, then sanitise the HTML with a library (e.g. HTMLPurifier)**

| Aspect | Assessment |
|--------|------------|
| **Advantages** | Could allow a curated set of HTML tags in pages |
| **Disadvantages** | New dependency and configuration; more moving parts than the current need justifies |
| **Implementation Difficulty** | Medium |

#### Decision

**Option B.** CommonMark's own `html_input: strip` and `allow_unsafe_links: false` cover the need. Raw HTML in pages is not a feature anyone asked for; if it becomes one, Option C can be revisited.

#### Verification

Five unit tests in `tests/Unit/Features/Wiki/ContentTest.php` check that Markdown still renders, that `<script>` and `<img onerror>` are removed, that `javascript:` links are dropped, that normal https links are kept, and that empty content renders empty.

#### Follow-ups

- Existing pages that relied on raw HTML will now lose it. Existing page content has not been audited; check any pages that were created before this change.


---

### Linking Wiki Pages to Distributions

**Date:** 2026-10-23
**Author:** deltaog-117
**Status:** Confirmed

#### Context / Background

The "distro identity" feature (accent colours and an infobox) needs to know which distribution a wiki page is about. Wiki slugs are free-form (the editor suggests `ubuntu-linux`) while distribution slugs are short (`ubuntu`), so the two do not line up by themselves.

#### Options Considered

| Option | Approach | Advantages | Disadvantages | Difficulty |
|--------|----------|------------|---------------|------------|
| A | Match on slug | No schema change, no editor UI | Silent mismatches; renaming a page severs the link | Easy |
| B | Nullable `distribution_id` on `wiki_pages`, chosen in the editor | Explicit and rename-safe; one distro can have many pages | Migration and editor change; Wiki now depends on DistroComparison | Medium |
| C | Nullable `page_id` on `distributions` | One canonical page per distro | No cheap reverse lookup; needs a manual fill step | Medium |

#### Decision

**Option B.** It is the only option that stays correct when slugs change. The cost is a one-way dependency from Wiki to DistroComparison (`Page::distribution()`); the Wiki feature can no longer be deleted without touching that relation.

#### Consequences

- The foreign key uses `nullOnDelete`, so deleting a distribution never deletes the articles written about it.
- The accent colour is stored as `#rrggbb` and wrapped in the `Accent` value object, which is the only way to produce the inline `--distro-accent` style; this keeps untrusted strings out of CSS.
- `scripts/check` now runs PHPUnit only. Pint was left out on purpose: it fails on 34 existing files because the repo has no `pint.json`, and reformatting them does not belong in this change (see ROADMAP).
- The migrations were not run against the MySQL development database in this session.

---

### Shipping Wiki Content With the Repository

**Date:** 2026-10-23
**Author:** deltaog-117
**Status:** Confirmed

#### Context / Background

The wiki had two articles (NixOS, Artix), both living only in one development database. The goal was to add the mainstream distributions plus independent and niche ones, and to correct the two existing articles.

#### Options Considered

| Option | Approach | Advantages | Disadvantages | Difficulty |
|--------|----------|------------|---------------|------------|
| A | Write straight into the database through the page actions | Fastest | Articles exist only on one machine | Easy |
| B | Markdown files with front matter plus a seeder that uses the same actions | Reviewable in git, reproducible on any clone, safe to re-run | New parser and seeder | Medium |
| C | Articles as PHP arrays in a seeder | No parser | Long Markdown in PHP is awkward to review | Easy |

#### Decision

**Option B.** Content that defines what the project shows should be reviewable and survive a fresh clone. The slug is the file name and distributions are referenced by slug, so a typo is a loud error instead of a silent missing infobox. The seeder only creates missing pages and only fills an unset distribution link, because readers may have edited pages.

#### Consequences

- Facts that change were verified against sources before writing; time-sensitive claims (releases, project status) carry their date or a "check the project site" note. Claims that could not be confirmed (for example Void's default C library) were left out.
- The two rewrites were applied to the existing pages once through `UpdatePage`; the seeder alone would have skipped them.
- A global commitlint hook rejected the wiki repository's commit messages, which exposed that saving a page writes the database before the commit and so can leave a row without history. The messages now follow Conventional Commits. Skipping the hook was considered and declined, since it would bypass a protection the owner configured. Making the save atomic is on the roadmap.
- Article authors are fictional bylines, kept separate from the repository's commit authorship.

---

### Distro Logos

**Date:** 2026-10-23
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Official logos are bundled as static SVGs named by distro slug in `public/images/distros/` (sources: Simple Icons, plus Wikimedia Commons for Guix, postmarketOS, Chimera and Puppy; the Guix file is cropped to its symbol) and drawn with a CSS `mask`, so they follow the accent colour and the theme with no inline SVG or runtime fetch. `Distribution::logoUrl()` returns null when no file exists and the views fall back to the initial.

#### Trade-offs

Tiny Core publishes no vector logo, only a raster wordmark on its wiki, so its "core" wordmark (tagline cropped off) is bundled as a PNG. A mask would turn it into a blob, so raster logos render as a plain image on a light chip; `logoUrl()` prefers `.svg` and falls back to `.png`. Logos remain trademarks of their projects.

---

### Encyclopedia Home Page

**Date:** 2026-10-24
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

The gradient hero (emoji pill, gradient headline, three buttons) is replaced by a masthead in the style of an encyclopedia main page: serif wordmark and tagline, the search box, and a plain "N distributions · N articles" line. Below it sit a featured article, a Portals list (compare, family tree, terminal), a "Browse by family" index and the full article grid.

#### Reasoning

The hero read as a generic landing page and its gradient clashed with the reference-work feel. Reference sites lead with search and content, so the page now shows real articles at the top and keeps colour for the per-distro accent only. Headings (h1–h4) use Source Serif 4 site-wide; body text stays Inter.

#### Consequences

- The featured article rotates by day of the year over articles linked to a distribution (`GetFeaturedPage`), so nothing needs curating. Its excerpt is the first Markdown paragraph as plain text (`Content::toExcerpt()`).
- Families come from `based_on`, with qualifiers folded in ("Ubuntu (LTS)" becomes "Ubuntu"); standalone distros and unlinked pages go last (`GroupPagesByFamily`). A stored parent slug, already on the roadmap for the family tree, would make this exact.
- The logo mark markup moved into one partial shared by the card and the featured article.
- The wordmark and tagline use Space Grotesk (a geometric sans) instead of the serif, which stays on article and section headings. The logo is a single original SVG, a penguin at a laptop showing a terminal prompt on an indigo badge (Linux plus a reference you consult), drawn from scratch rather than reusing Tux, and used as an image in the navbar and masthead and as the favicon. Its fixed indigo works on both themes, so it needs no per-theme variant.

---

### Family Grouping by Root Ancestor

**Date:** 2026-10-24
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

"Browse by family" groups each article by the root of its `based_on` chain instead of by its direct parent.

#### Reasoning

Grouping by direct parent listed Ubuntu both as a family (holding Linux Mint) and as a member of Debian, and put Debian itself under "Independent". Following the chain to the root removes the duplicate heading and shows Mint as the Debian descendant it is. A stored `family` column would duplicate data already in `based_on`, and a nested tree overlaps the family-tree page.

#### Consequences

- Names are matched case-insensitively with a trailing " Linux" ignored, so "Arch" and "Arch Linux" are the same distribution; qualifiers such as "(LTS)" are still folded.
- A parent without an article of its own (for example Red Hat) names the family as written.
- A root with no derivatives goes to "Independent"; a cycle in the data stops at the last distribution reached.
- Only distributions that have an article take part in the chain, so a missing article for an intermediate parent would split a family. The stored parent slug already on the roadmap would remove that limit.

---

### Sans-Serif Headings and Article Spacing

**Date:** 2026-10-24
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Headings (h1–h4), the featured-article title and the section titles use Space Grotesk, and the featured excerpt uses Inter. Source Serif 4 is removed from the font request and the Tailwind config. In rendered articles the block after a heading sits close to it, headings get a wider gap above, and the body is capped at `max-w-prose` with `leading-8`.

#### Reasoning

The serif face read as dated for a tool-like app and clashed with the Space Grotesk wordmark. In articles every block shared one gap, so a heading was as far from its own paragraph as from the previous section and sections blurred together.

#### Consequences

- One display face and one text face remain, which also drops a font download.
- The heading-to-text gap comes from `.article > :is(h1, h2, h3, h4) + *`; paragraph spacing is unchanged.
- `public/css/app.css` is rebuilt with `npm run css:build`. `npm run build` (Vite) fails on a `laravel-vite-plugin` "./fonts" import with or without these changes, and is left alone.

---

### Family Tree Parent Resolution

**Date:** 2026-10-24
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

The tree is built by `BuildFamilyTree`, which resolves each `based_on` by a normalised name (lower-case, `(qualifier)` and a trailing "Linux" removed) and nests the result recursively. A parent with no row becomes a bare node; "Independent" and loops make roots.

#### Reasoning

The old builder appended children to a copy of the parent and only matched exact slugs, so Linux Mint, Fedora and openSUSE ended up detached. The same normalisation already drives `GroupPagesByFamily`, so both views agree.

#### Consequences

- No schema change; a stored parent slug stays an option if names drift.
- The normalisation exists in two classes and could be shared if it changes again.
- The page is a force-directed map instead of a chart with one virtual root: families are pinned to anchors and the childless distros to a central ring, so clusters cannot overlap. Selecting a node opens a side panel, not the article, so the map stays in view; D3 stays on the CDN, pinned to 7.9.0.
- Nodes show the bundled logos: vector logos are painted white through an SVG alpha mask on an accent-coloured disc, the same idea as the CSS mask used on wiki cards; the raster Tiny Core logo sits on a white disc with an edge, and a distro with no logo shows its initial.

---

### Terminal Simulator Shell

**Date:** 2026-10-24
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

The simulator stays a browser-only mock. Input is tokenized (quotes group words; `|`, `>` and `>>` are operators), split into pipeline segments and run left to right; each command is `(args, env, stdin)` and returns a string that becomes the next command's stdin or is written to a file. Tab completes command names for the first word and paths after it. The window dots are real buttons: close ends the session, minimize collapses the window to its title bar and maximize makes it fixed and full-size.

#### Reasoning

A string-in, string-out command table is enough for teaching pipes and redirects and keeps every command a pure function of its arguments and the in-memory filesystem. A real shell grammar (variables, globbing, subshells) would add far more code than the page needs.

#### Consequences

- The filesystem is mutable but lives only in the page, so `touch`, `mkdir` and redirects are lost on reload.
- Commands see `env.piped` so `ls` prints one name per line when its output is piped or redirected, as a real `ls` does.
- Not supported: variables, globbing, `&&`, `;`, `<`, `2>`, background jobs and Ctrl+C.
- The scrollbar is styled with `scrollbar-color` and `::-webkit-scrollbar` on `.xterm-viewport`; `public/css/app.css` is rebuilt with `npm run css:build`.

---

### Command Palette

**Date:** 2026-10-25
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Ctrl/Cmd+K opens a Livewire modal (`CommandPalette`) in the shared layout. Two single-purpose actions feed it: `FilterPaletteCommands` filters a fixed list of routes and a theme toggle by label and keywords (guests also see Login and Register), and `FindPalettePages` matches page titles with `LIKE`. Alpine holds the open state and the highlighted row; the layout script only dispatches a `palette-open` event.

#### Reasoning

Three options were weighed: a Livewire modal (A), a client-side palette over a pre-built JSON index (B) and a hybrid (C). A reuses the search stack and needs no JS build or index that can go stale. A round-trip per keystroke is cheap for a wiki this size, and the input is debounced by 150 ms.

#### Consequences

- Page lookup uses `LIKE` on the title, not the FULLTEXT index behind the search page, so it also works on SQLite and matches partial words. It does not search article bodies; the search page still does.
- `LIKE` wildcards in the input are escaped with `!`, which means the same in MySQL and SQLite, unlike a backslash.
- The command list is hard-coded in `FilterPaletteCommands`; a new nav item has to be added there too.
- Not yet covered by a browser or feature test: the feature migrations are MySQL-only, so only the pure parts have unit tests.

---

### Revision History

**Date:** 2026-10-25
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Content stays in the wiki Git repository. A `wiki_revisions` row per commit (page_id, sha, path, author, summary, created_at) points at it, and old text is read with `git show <sha>:<path>`. `GitService::commitFile()` now returns the new sha, or null when nothing changed, and `CreatePage` and `UpdatePage` record a row from it. Differences are computed in PHP by `LineDiff` (longest common subsequence over lines). Restoring goes through `UpdatePage`, so a restore is a new revision and history is never rewritten.

#### Reasoning

Three options were weighed: Git only (A), a database copy of every revision (B) and the hybrid (C). Copying bodies into the database duplicates what Git already stores, while Git alone makes lists, authors and summaries slow to query. The slim row gives later features (recent changes, rollback, permalinks) what they need without a second copy of the text. Diffing in PHP avoids parsing `git diff` output and can be property-tested without a repository.

#### Consequences

- The `path` is stored per revision because a rename changes it; reading an old revision uses the path at that commit.
- Only shas that belong to the page (matched against its rows) are ever passed to Git, and routes accept only 40-hex shas.
- Commit and row are not atomic: a failure between them leaves a commit without a row (`wiki:backfill-revisions` repairs this). The page-save atomicity item stays on the roadmap.
- Pages with commits older than this change show an empty history until `php artisan wiki:backfill-revisions` is run once.
- Revert, history and diff have no permission check, like editing today; roles are a separate roadmap item.
- A diff over about a million line pairs falls back to one replaced block. Restore is not covered by an automated test (the feature migrations are MySQL-only); it was not run against the dev wiki repo.

---

### Discovery Pages

**Date:** 2026-10-25
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Random article, Recent changes and All pages (A–Z) live at `/random`, `/recent-changes` and `/all-pages`, served by a new `DiscoveryController`. Each has one Action: `GetRandomPage` picks a slug from the collection it is given, `ListRecentChanges` pages through `wiki_revisions` newest first, and `GroupPagesAlphabetically` groups titles by first letter with non-letters under `#`. The views are plain Blade; the home page masthead and the command palette link to them.

#### Reasoning

Three options were weighed: Actions with a controller and Blade views (A), one tabbed Livewire component (B) and query scopes on `PageController` and `Page` (C). A matches how the Wiki feature is already built, gives each page a bookmarkable URL and works without JS. The routes sit outside `/wiki/` because `/wiki/{slug}` would otherwise swallow a page whose slug is `random`.

#### Consequences

- Recent changes shows one row per revision, so a page saved ten times appears ten times. Its "Changes" link needs the previous revision of the same page, which a correlated subquery supplies as `previous_sha` (checked against the dev MySQL data, not by an automated test).
- Pages saved before `wiki:backfill-revisions` was run have no rows and do not appear in Recent changes.
- Only the pure Actions have unit tests; the controller and views are not covered, because the feature migrations are MySQL-only.
- The list of palette commands grew by three, so the palette test's expected list changed with it.
- Random chooses among all pages with equal weight and can return the page you are already reading.

---

### Wiki Links

**Date:** 2026-10-25
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

`[[Target]]` and `[[Target|label]]` are parsed by a CommonMark inline parser (`WikiLinkParser`), so code spans and fenced code are never rewritten. The target is turned into a slug with `Str::slug` and checked through the `Slug` value object. A `wiki_links` table (`page_id`, `target_slug`) records each page's outgoing links and is kept in sync by a `PageObserver` on `Page::saved`. `RenderContent` looks up which linked slugs exist and passes them to `Content::toHtml()`, so a link to a missing page renders as a red link to `/wiki/create?slug=...`. "What links here" is one indexed query (`ListBacklinks`).

#### Reasoning

Three options were weighed: a link table synced by each save action (A), a render-time parse with a `LIKE` search for backlinks (B), and a link table synced by an observer (C). B needs a full scan for every backlink lookup and cannot serve later features such as categories. A needs every write path (create, update, revert, seeder) to remember the sync. C covers all of them because each saves through the model, and `Content` stays free of database access: the known slugs are passed in.

#### Consequences

- The table stores a target slug, not a page id, so renaming or deleting a page needs no fix-up: links to it turn red, and links to a new page with that slug turn blue. The text of other pages is not rewritten on a rename.
- Existing pages are not indexed until `php artisan migrate` and `php artisan wiki:sync-links` are run once.
- Without a list of known slugs, `toHtml()` treats every link as existing (used for excerpts, where only the label text matters).
- Only the parsing and rendering have unit tests; the observer, the backlinks page and the editor prefill need the MySQL test database and were not run automatically.
- None of the 21 shipped articles use `[[links]]` yet; the roadmap lists that as a follow-up.

---

### Citations and References

**Date:** 2026-10-25
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

`Content::render()` registers the CommonMark `FootnoteExtension` next to `WikiLinkExtension`. `[^1]` becomes a superscript link and each `[^1]: text` definition becomes an entry in a `footnotes` list at the end of the article, with a back-link. The "References" heading is drawn by CSS (`.footnotes::before`) and the default `<hr>` is hidden.

#### Reasoning

The library already ships a footnote parser and renderer, so no custom code was needed and the one-option approach was taken without a COA table. Rendering the heading in CSS avoids a post-processing step on the HTML and keeps `toHtml()` output free of extra markup; the cost is that the word "References" is not in the HTML, so it is not selectable text.

#### Consequences

- Footnote text goes through the same sanitising as the body: raw HTML is stripped and unsafe link schemes are dropped (covered by a test).
- `[^1]` does not clash with `[[wiki links]]`: that parser only matches a double bracket. Links inside a footnote still count for "What links here".
- The table of contents only lists `h2`/`h3`, so References is not in it.
- The excerpt on cards is the first `<p>`, so footnote markers appear as plain digits glued to the text ("rolling1"). Not handled yet.
- `npm run build` fails in this checkout (`laravel-vite-plugin/fonts` is missing from `node_modules`, with or without this change), so the new CSS was not compiled or looked at in a browser.

---

### Categories and Tags

**Date:** 2026-10-25
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Relational storage (COA A): a `wiki_categories` table (slug, name, optional description) and a `wiki_category_page` pivot. The editor has one comma-separated "Categories" field; `ParseCategoryNames` turns it into names keyed by slug and `SyncPageCategories` creates missing categories and syncs the pivot. `/categories` lists categories that have pages, `/categories/{slug}` lists their articles, and the article header shows the categories as chips.

#### Reasoning

COA B (a `[[Category:X]]` tag in the body) would version membership in Git, and COA C (a JSON column) is the cheapest, but only a table gives category pages with a description and room for parent categories. Categories are synced by the editor, not by the `Page` observer, because membership is not part of the Markdown body.

#### Consequences

- Category membership is not in Git, so it does not show in history or diffs and is not restored by "Restore".
- Two spellings with the same slug ("Init System", "init-system") are one category; the first name wins. At most 20 categories per page.
- A category left without pages is hidden from `/categories` but not deleted; its page still opens.
- Descriptions have no editing UI yet; set them in the database for now.
- Only `ParseCategoryNames` has unit tests (including a randomised one). The sync, controller and views need the MySQL test database, so they were checked by compiling the views and running the migration against SQLite with `--pretend`, not end to end.
- Run `php artisan migrate` to create the tables.

---

### Cross-links in the Shipped Articles

**Date:** 2026-10-26
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

The 21 seeded articles now use `[[slug|label]]` to link related distributions. Each target is linked once per article, at its first mention in prose, by hand rather than by a find-and-replace script. `ShippedArticleLinksTest` checks that every link target is a file in `database/content/wiki/`.

#### Reasoning

A script that links every distro name would also hit headings, bold lead-ins, tables and unrelated uses of a word ("Mint"). Linking only the first prose mention matches how encyclopedias read and keeps the pages calm. The slug form is used because titles and slugs differ (NixOS is `nix-os`, Gentoo Linux is `gentoo`).

#### Consequences

- Tables are not linked: `|` inside a cell would split the cell. The comparison tables stay plain.
- `WikiContentSeeder` never overwrites an existing page, so a database that was seeded earlier keeps the unlinked text until the pages are edited. A fresh install gets the links, and the `Page` observer fills `wiki_links` as it seeds.
- Fedora, Gentoo, Slackware, Void Linux and Tiny Core Linux have no outgoing links, because they only name other distributions in tables. Other articles link to them.
- Not checked in a browser; the test covers the link targets only.

---

### Per-distro Landing Pages

**Date:** 2026-10-26
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Each distribution has its own page at `/distro/{slug}`. It shows the infobox, an ancestry trail (root to parent), the derivatives and siblings, and related articles, with a button to the full article. The infobox title, the comparison-table name and the article header chip link to it. The family position is plain linked lists (COA B), not a mini tree (COA C).

#### Reasoning

The page works without JavaScript and for a distribution that has no article, which a panel on the article (COA A) could not do. `BuildDistroProfile` resolves `based_on` the same way `BuildFamilyTree` does (qualifiers folded, "Arch" equals "Arch Linux", a parent without a row kept as a bare name, loops ended), and returns a `DistroProfile` value object so the view cannot receive a half-built result. Related articles come from `ListRelatedPages` in the Wiki feature: pages that link to the article, pages it links to, and pages sharing a category.

#### Consequences

- The name-normalising rules now exist in three places (`BuildFamilyTree`, `GroupPagesByFamily`, `BuildDistroProfile`). They agree today; sharing them is a later cleanup.
- A distribution with several pages uses the first one as its article.
- `BuildDistroProfile` has unit tests, including a seeded random-data test of its invariants. `ListRelatedPages`, the controller and the views need the MySQL test database, so they were checked by compiling the views and listing the route, not by loading the page.
- The family tree nodes still open the article; linking them to the landing page is not done.

---

### Roles and Edit Permissions

**Date:** 2026-10-26
**Author:** deltaog-117
**Status:** Confirmed

#### Decision

Each account holds exactly one of three roles, stored in `users.role` and cast to the `Role` enum (COA A). `PagePolicy` holds the rules: create and edit need an editor, restoring a revision and protecting a page need an admin, and a protected page can only be edited by an admin. Editing is no longer open to guests. Roles are set with `php artisan user:promote`; there is no admin screen yet.

#### Reasoning

An enum column cannot hold a fourth value, and the policy is the single place later features (talk pages, moderation) will ask "may this user do X". A permissions package (COA B) would add a dependency and tables for roles nobody has asked for. Two boolean flags (COA C) allow an admin who is not an editor. The role is left out of the mass-assignable fields, so a form cannot set it; only the command and the seeder can.

#### Consequences

- The Livewire editor checks the policy in `mount()` and again in `save()`, because Livewire requests do not pass through the route middleware.
- Registration creates readers, so a new visitor can read but not write until an admin promotes them. The first admin comes from `MainEditorSeeder` (local only, throwaway password) or the command.
- The main account was made an admin, not just an editor, because restoring and protecting are admin-only; `user:promote` lowers it.
- Red links in articles and the "Write one" link on a distro page still point at the editor; a reader who follows one gets a 403.
- Existing accounts became readers when the migration ran. Nobody else can edit until an account is promoted.
- The policy and role are unit-tested. The routes, the Livewire component and the views need the MySQL test database; they were checked by hand: guests are redirected from create and edit, and the policy answers were read for every account.
- The development database still lacks the categories and links tables from earlier features (their migrations are pending, and the duplicate `create_users_table` stub would fail), so article pages return 500 there until that is sorted out.
