# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
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
