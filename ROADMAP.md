# Roadmap

This document outlines the future direction of Tuxpedia.
Items are organized by priority, not by timeline. Items marked *(proposed)* have not been confirmed by the author.

---

## ✅ Completed (Milestones Achieved)
- Discovery pages: Random article, Recent changes (from `wiki_revisions`) and All pages A–Z, linked from the home page and the command palette
- Revision history: history page, read-only old revisions, line diff and restore, backed by a slim `wiki_revisions` table that points at Git commits; edit summaries; `wiki:backfill-revisions` for older commits
- Family-tree map shows a logo on parent nodes that have no distribution row (Red Hat); SVG mask ids are sanitised
- Command palette: Ctrl/Cmd+K opens a Livewire modal that searches page titles and jumps to the main routes or toggles dark mode, with arrow-key navigation
- Family-tree parent matching fixed: `BuildFamilyTree` normalises `based_on` (qualifiers, "Linux" suffix, parents without a row) and breaks loops
- v0.1.0: wiki CRUD with Git versioning, distro comparison table, full-text search, D3 family tree, authentication, "changed since last visit" badges, terminal simulator
- UI redesign "Modern Docs": design tokens, dark mode, hero, article layout with table of contents, mobile navigation, themed D3 tree and data table
- Markdown sanitisation: raw HTML is stripped and unsafe link schemes are dropped when a page is rendered (`Content::toHtml()`, covered by unit tests)
- Distro identity: brand accent colour per distro (wiki card, article header chip, comparison table dot, family-tree node) and an infobox sidebar on distro pages. A wiki page links to a distribution through an optional `distribution_id`, chosen in the editor
- Distro logos: all 21 distributions show their official logo (bundled SVG, CSS-masked; PNG for Tiny Core) on the wiki card and infobox
- Encyclopedia home page: masthead, day-rotating featured article, Portals and a "Browse by family" index replace the gradient hero; serif headings, Space Grotesk wordmark and a Tuxpedia logo (navbar, masthead, favicon)
- Browse by family groups by root ancestor (Mint, Ubuntu and Tails under Debian) instead of by direct parent
- Sans-serif headings and a clearer article layout (heading spacing, line length)
- Terminal simulator: padded screen, dark scrollbar, working window buttons (close, minimize, maximize), about 30 more commands, a writable in-memory filesystem, pipes and redirects, quoted arguments and Tab completion
- Project check command `scripts/check` (PHPUnit); stale `ExampleTest` corrected
- Wiki content library: 21 reviewed articles (8 mainstream, 13 independent or niche distros, including rewrites of NixOS and Artix) stored as Markdown in `database/content/wiki/` and imported by `WikiContentSeeder`; 12 new distribution rows
- Wiki commit messages follow Conventional Commits, so they pass the global commitlint hook

---

## 🔥 High Priority (Critical)
- Wiki links `[[Debian]]` – render to article links, red links for missing pages, and a "What links here" list (needs a link table)
- Citations and references – `[^1]` footnotes rendered with a References section
- Categories and tags – attach categories to articles; category pages (init system, package manager, desktop)
- Talk page per article – discussion separated from the article, used to flag stale or disputed content
- Roles and edit permissions – reader, editor and admin roles; page protection; rollback for moderators
- Glossary and disambiguation pages – ambiguous terms (Arch/Artix, "Mint") and concept entries (kernel, init)
- Per-distro landing pages – infobox, family-tree position and related wiki pages on one page
- Print, export and permalinks – print stylesheet, Markdown/PDF export, stable per-revision URLs
- Visual verification pass *(proposed)* – screenshot every page in light and dark and refresh `screenshots/` and the README

---

## 🟡 Medium Priority (Important)
- Make page saves atomic *(proposed)* – `CreatePage` and `UpdatePage` save to the database before committing to the wiki repository, so a failed commit leaves a database row with no history
- Keep articles current *(proposed)* – several articles quote release status (Fedora, Ubuntu, Alpine, Tails, Solus, Slackware 16); review them each quarter
- Code style gate *(proposed)* – add a `pint.json` and format the existing code so `pint --test` can join `scripts/check` (34 files fail the default preset today)
- Feature tests for the page-to-distribution link *(proposed)* – the feature migrations are MySQL-only (FULLTEXT), so these need a MySQL test database
- Feature tests for the wiki, search and auth flows *(proposed)* – the `tests/Feature` and `tests/Unit/Features` folders are almost empty
- Server-side table of contents *(proposed)* – move heading extraction out of the client script if pages need SEO or no-JS support
- Sort direction indicators in the distro comparison table

---

## 🟢 Low Priority (Nice‑to‑Have)
- Syntax highlighting for code blocks in articles

---

## 🔭 Long‑Term Vision
