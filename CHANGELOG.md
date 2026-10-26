# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Talk pages** – every article has a Talk page (`/wiki/{slug}/talk`, linked from the article header as "Talk (n)") for discussion kept apart from the article: any topic, such as a suggestion, a question or a correction. A topic has a title and an opening message, and replies are listed under it; topics sort by latest activity. Any logged-in account may post, even on a protected article, and guests can read; an admin can delete a topic or a reply. Posts are plain text (printed escaped, never parsed as Markdown or HTML), titles are limited to 150 characters and messages to 5,000, and posting is limited to 10 a minute per user. Run `php artisan migrate` to create the `wiki_talk_topics` and `wiki_talk_replies` tables (`TalkTopic`, `TalkReply`, `TalkTitle`, `TalkBody`, `OpenTalkTopic`, `ReplyToTalkTopic`, `TalkController`, `PagePolicy::discuss()` and `moderate()`, with unit tests)
- **Roles and edit permissions** – every account has a role: reader, editor or admin (new `users.role` column, default reader). Creating and editing pages needs an editor; restoring an old revision and protecting a page need an admin. An admin can protect a page from its header (new `wiki_pages.protected_at` column), after which only admins can edit it. Guests who open the editor are sent to the login page, a reader gets a 403, and the Edit, New Page, Restore and Protect controls are hidden from anyone who may not use them. The editor checks the rule again on every save. `php artisan user:promote {email} {role}` changes a role; `MainEditorSeeder` creates a local `editor@tuxpedia.local` admin and refuses to run in production. Run `php artisan migrate` (`Role`, `PagePolicy`, `SetPageProtection`, `PromoteUser`, with unit tests)
- **Per-distro landing pages** – `/distro/{slug}` shows a distribution's infobox, its ancestry from the root down to its parent, its siblings and derivatives, and its related articles (pages that link to the article, pages it links to and pages in the same categories), with a button to the article; a distribution without an article offers a link to write one. The infobox title, the comparison-table name and the article header chip link to it (`BuildDistroProfile`, `DistroProfile`, `ListRelatedPages`, `DistroController`, with unit tests)
- **Cross-links in the shipped articles** – 23 `[[links]]` in 16 of the 21 seeded articles connect related distributions at their first prose mention (for example Ubuntu to Debian, postmarketOS to Alpine Linux, Guix System to NixOS), so "What links here" now has real data on a fresh install. Table cells are left plain. `ShippedArticleLinksTest` fails when a link points at an article that is not shipped. `WikiContentSeeder` never overwrites an existing page, so an existing database keeps the old text
- **Categories** – an article can belong to several categories, typed as a comma-separated list in the editor (new ones are created on first use). Category chips appear in the article header, `/categories` lists every category with its article count and `/categories/{slug}` lists its articles; All pages links to the index. Run `php artisan migrate` to create the `wiki_categories` and `wiki_category_page` tables (`Category`, `ParseCategoryNames`, `SyncPageCategories`, `CategoryController`, with unit tests)
- **Citations and references** – `[^1]` footnote markers in an article link to a "References" list at the end of the page, with a back-link from each entry; footnote text is sanitised like the rest of the article (`Content::render()` uses the CommonMark `FootnoteExtension`, styled in `app.css`, with unit tests)
- **Wiki links** – `[[Linux Mint]]` and `[[Debian|the universal OS]]` render as links to article pages; a link to a page that does not exist is a red link that opens the editor with the slug and a title filled in. Each article has a "What links here" page (`/wiki/{slug}/backlinks`) fed by a `wiki_links` table, which a `Page` observer keeps in sync on every save (editor, restore, seeder). Links inside code spans and code blocks are left alone. Run `php artisan migrate` and then `php artisan wiki:sync-links` once to index existing pages (`WikiLinkParser`, `WikiLinkExtension`, `SyncPageLinks`, `ListBacklinks`, `RenderContent`, `PageObserver`, with unit tests)
- **Discovery pages** – Random article (`/random`), Recent changes (`/recent-changes`, every saved revision across the wiki with a link to its diff) and All pages (`/all-pages`, A–Z with a letter jump bar); linked from the home page masthead and the command palette (`GetRandomPage`, `ListRecentChanges`, `GroupPagesAlphabetically`, `DiscoveryController`, with unit tests)
- **Revision history** – each article has a history page listing its saved revisions (commit, date, author, edit summary), a read-only view of any old revision, a line-by-line diff between a revision and the one before it, and a "Restore" button that saves an old revision as a new one. Content stays in the wiki Git repository; a slim `wiki_revisions` row per commit stores the sha, the file path at that commit, the author and the summary. The editor has a new "Edit summary" field. `php artisan wiki:backfill-revisions` creates rows for commits made before this change (`RecordRevision`, `ReadRevision`, `CompareRevisions`, `RevertPage`, `LineDiff`, with unit tests)
- **Logos for row-less parents** – family-tree nodes for parents without a distribution row (such as Red Hat) use a bundled logo from `public/images/distros/` when one exists (`BuildFamilyTree::bundledLogo()`); adds the Red Hat logo
- **Command palette** – Ctrl/Cmd+K opens a modal that filters the main routes and a dark-mode toggle, and finds wiki pages by title (the most recently updated pages when the query is blank); arrow keys, Enter and Esc work, and `/` still focuses the navbar search (`FilterPaletteCommands`, `FindPalettePages`, `CommandPalette`, with unit tests)
- **Terminal shell features** – pipes (`ls | grep doc`), output redirection (`>` and `>>`), quoted arguments and Tab completion for commands and paths in the terminal simulator; `grep`, `cat`, `head`, `tail` and `wc` read piped input, and `sort` and `uniq` are new
- **Terminal commands** – `mkdir`, `touch`, `rm`, `cp`, `mv`, `tree`, `head`, `tail`, `wc`, `grep`, `sort`, `uniq`, `id`, `hostname`, `date`, `uptime`, `free`, `df`, `ps`, `env`, `history`, `neofetch`, `which`, `man`, `cowsay`, `fortune` and `sudo`; `ls` gains `-a` and `-l`, `cat` takes several files; the filesystem is writable for the session
- **Terminal window buttons** – the red dot ends the session, the yellow one collapses the window to its title bar and the green one maximizes it (Esc leaves); each dot shows a glyph on hover and the title follows the working directory
- **Encyclopedia home page** – masthead with serif wordmark, tagline, search and article/distribution counts; a day-rotating featured article, a Portals list and a "Browse by family" index above the article grid (`GetFeaturedPage`, `GroupPagesByFamily`, `Content::toExcerpt()`, with unit tests)
- Source Serif 4 for headings (h1–h4); Space Grotesk for the wordmark, tagline and navbar brand
- **Tuxpedia logo** – a penguin at a laptop showing a terminal prompt, on an indigo badge (`public/images/tuxpedia-logo.svg`), shown beside the name in the navbar and masthead and used as the favicon, replacing the penguin emoji in the navbar
- **Official distro logos** – all 21 distributions show their official logo on the wiki card and infobox instead of the title's initial: SVGs (Simple Icons, Wikimedia Commons) in `public/images/distros/` painted through a CSS mask, and a raster PNG for Tiny Core, which has no vector logo, shown as-is on a light chip
- **Wiki content library** – 19 new articles (Debian, Ubuntu, Arch Linux, Fedora, openSUSE, Linux Mint, Gentoo, Alpine Linux, Void Linux, Slackware, Guix System, Qubes OS, Tails, postmarketOS, Chimera Linux, Puppy Linux, Bedrock Linux, Solus, Tiny Core Linux), kept as Markdown with front matter in `database/content/wiki/` and imported by `WikiContentSeeder` (never overwrites an existing page)
- **12 new distributions** in the comparison table, family tree and infobox: Artix, Void, Slackware, Guix System, Qubes OS, Tails, postmarketOS, Chimera, Puppy, Bedrock, Solus, Tiny Core
- `ArticleFile` value object (front-matter parser) with tests that check every shipped article parses, names a seeded distro and contains no raw HTML
- **Distro identity** – each distribution has a brand `accent` colour (validated `#rrggbb` via the `Accent` value object) shown on its wiki card, article header chip, comparison-table dot and family-tree node
- **Distro infobox** – Wikipedia-style sidebar on linked wiki pages (based on, release model, package manager, default desktop, architecture, website)
- **Page-to-distro link** – optional `wiki_pages.distribution_id` (nullable foreign key, set to null if the distro is deleted) with a "Distribution" dropdown in the wiki editor
- `scripts/check` project check (runs PHPUnit) and unit tests for `Accent`
- **Dark mode** – class-based theme toggle in the navbar; respects the system preference on first visit and remembers the choice in `localStorage` (applied before first paint, so no flash)
- **Design tokens** – colours defined once as CSS variables in `resources/css/app.css` and exposed as `tux-*` Tailwind colours, so every component follows the theme
- **Article layout** – styled Markdown (`.article`) plus an auto-generated "On this page" table of contents with scroll-spy
- **Keyboard search** – press `/` or `Ctrl/⌘+K` to focus the search box
- **Mobile navigation** – hamburger menu (previously the nav links were hidden below `md`), active-page highlighting and a skip-to-content link
- **Terminal window chrome** – title bar and traffic-light dots around the xterm container
- **Unit tests for Markdown rendering** – `tests/Unit/Features/Wiki/ContentTest.php` covers rendering, script stripping and link handling

### Changed
- Terminal: the screen is padded inside the window and uses a monospace stack with a block cursor; the scrollbar is dark to match the emulator; the prompt shows the working directory
- Terminal: paths such as `a/../b` and `~` resolve correctly, and multi-line output no longer staircases
- **Family tree map** – the D3 chart (a made-up "Linux" root with 17 curves, labels over links, double scrollbars) is now an interactive map: each family is a cluster anchored around a central ring of independent distros, nodes show each distro's official logo on its accent colour (initial when there is no logo, raster Tiny Core logo on white) and are sized by number of derivatives, hovering highlights a lineage, and selecting a node opens a side panel with its summary, parent, derivatives and a link to its article. Zoom, pan and drag; the layout settles before the first paint and fits the view (`BuildFamilyGraph`, with unit tests)
- Home page: the gradient hero is replaced by the encyclopedia masthead; "Wiki pages" is now "All articles"
- **NixOS and Artix articles rewritten** – removed leftover chatbot text and layout `<br>` tags, clarified that Asterinas NixOS is not an official NixOS project, corrected the Artix GNOME-removal date to October 2025 and noted that the XLibre default was later reversed
- Wiki repository commit messages now read `docs(wiki): create page slug` (was `Create page: slug`) so they satisfy the global commitlint hook
- `DistributionSeeder` exposes its rows through `catalogue()`
- `DistributionSeeder` fills in the accent colour of rows seeded earlier without overwriting other fields
- `ExampleTest` now asserts that `/` redirects to the wiki index (it expected a 200 that the route never returned)
- **UI redesign ("Modern Docs")** – glass navbar, rounded cards with hover lift, refined buttons, badges, alerts and form fields across wiki, search, auth and distro pages
- **Distro table** – sticky-style header, chips for "Based On", monospace package manager and architecture columns
- **Family tree** – D3 output is styled through CSS classes so it follows light and dark themes
- `tux-blue` is now an alias for the new indigo accent (`tux-accent`)
- Tailwind now also scans Laravel/Livewire pagination views so their `dark:` variants are generated

- **Browse by family follows the whole ancestry** – `GroupPagesByFamily` now walks the `based_on` chain to its root, so Linux Mint, Ubuntu and Tails sit together under Debian and Artix under Arch Linux, instead of Mint appearing under a separate "Ubuntu" family; roots with no derivatives share "Independent"; loops in the data are cut off

- **Sans-serif headings and clearer article sections** – headings and the featured-article title now use Space Grotesk, and the featured excerpt uses Inter in the muted colour, so the serif face (Source Serif 4) is no longer loaded; article headings get a wider gap above and a tight gap below so each binds to its own text, h2 gets more padding under its rule, and the body is capped at a readable line length with looser leading

### Deprecated
- (None)

### Removed
- (None)

### Fixed
- `php artisan migrate` stopped with "table `users` already exists" and left every later migration pending: a stray `2026_08_25_192632_create_users_table` migration repeated the `users` table that Laravel's own `0001_01_01_000000_create_users_table` already creates. The duplicate is removed
- Saving a page whose text did not change (for example only its distribution link) no longer fails with "nothing to commit": `GitService::commitFile()` skips the commit and returns null
- Every page returned HTTP 500 because the command palette passed a `Title` object where `PaletteItem::link()` expects a string; the title is now cast to a string
- Family-tree logo mask ids are sanitised, so a node id with unusual characters can no longer break its `url(#…)` reference
- **Family tree** – derivatives were detached or lost: parents named "Ubuntu (LTS)", "Arch" or "Red Hat" matched nothing, and children were appended to a copy of the parent. Tree building is now `BuildFamilyTree`, which folds qualifiers, matches "Arch" to "Arch Linux", adds a node for a parent with no row, and breaks loops (with unit tests)
- Saving a wiki page failed after the database write whenever a Conventional Commits hook was configured globally, because the page's commit message was rejected
- Markdown articles previously rendered unstyled because `.prose` was used without the typography plugin

### Security
- Wiki Markdown is now rendered through `Content::toHtml()`, which strips raw HTML and drops `javascript:`, `data:` and `vbscript:` links. Previously page content was printed unescaped, so a page could inject script into every visitor's browser.

---

## [0.1.0] - 2026-08-30

### Added
- **Wiki CRUD** – Full Markdown editing with Git versioning (every change committed to a local Git repository)
- **Distro Comparison Table** – Sortable, filterable, searchable table of Linux distributions (seeded with 9 major distros)
- **Full‑text Search** – MySQL FULLTEXT search with relevance ranking for wiki pages
- **Distro Family Tree** – Interactive D3.js tree visualisation showing distribution lineages
- **User Authentication** – Login, Register, Logout with Livewire and custom User model
- **"Changed Since Last Visit"** – Green badge on wiki pages updated after the user's last visit
- **Terminal Simulator** – Fully functional Linux terminal in the browser using xterm.js with a virtual filesystem and command parser
- **Light Theme with Dark Terminal** – Clean, accessible light theme for the main interface, with a dark terminal for an authentic Linux experience
- **Feature‑First Architecture** – Vertical‑slice organisation under `app/Features/`; each feature is self‑contained and can be deleted without breaking the rest
- **Shared Infrastructure** – Reusable code in `app/Shared/` (database, logging, utilities, Git service)
- **Livewire Components** – Interactive components for search bar, comparison table, terminal, and auth forms
- **Blade Views** – Clean, responsive views for all features
- **Comprehensive Documentation** – README with installation, usage, architecture overview, and screenshots
- **MIT License** – Open‑source license for the project

### Changed
- (No changes – initial release)

### Deprecated
- (None)

### Removed
- (None)

### Fixed
- (None – initial release)

### Security
- (None – initial release)
