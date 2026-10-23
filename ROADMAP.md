# Roadmap

This document outlines the future direction of Tuxpedia.
Items are organized by priority, not by timeline. Items marked *(proposed)* have not been confirmed by the author.

---

## ✅ Completed (Milestones Achieved)
- v0.1.0: wiki CRUD with Git versioning, distro comparison table, full-text search, D3 family tree, authentication, "changed since last visit" badges, terminal simulator
- UI redesign "Modern Docs": design tokens, dark mode, hero, article layout with table of contents, mobile navigation, themed D3 tree and data table
- Markdown sanitisation: raw HTML is stripped and unsafe link schemes are dropped when a page is rendered (`Content::toHtml()`, covered by unit tests)
- Distro identity: brand accent colour per distro (wiki card, article header chip, comparison table dot, family-tree node) and an infobox sidebar on distro pages. A wiki page links to a distribution through an optional `distribution_id`, chosen in the editor
- Distro logos: all 21 distributions show their official logo (bundled SVG, CSS-masked; PNG for Tiny Core) on the wiki card and infobox
- Project check command `scripts/check` (PHPUnit); stale `ExampleTest` corrected
- Wiki content library: 21 reviewed articles (8 mainstream, 13 independent or niche distros, including rewrites of NixOS and Artix) stored as Markdown in `database/content/wiki/` and imported by `WikiContentSeeder`; 12 new distribution rows
- Wiki commit messages follow Conventional Commits, so they pass the global commitlint hook

---

## 🔥 High Priority (Critical)
- Visual verification pass *(proposed)* – screenshot every page in light and dark and refresh `screenshots/` and the README

---

## 🟡 Medium Priority (Important)
- Make page saves atomic *(proposed)* – `CreatePage` and `UpdatePage` save to the database before committing to the wiki repository, so a failed commit leaves a database row with no history
- Fix family-tree parent matching *(proposed)* – `FamilyTree::buildTree()` loses nodes whose `based_on` matches a distro by name only, and `Linux Mint` uses `Ubuntu (LTS)`, which matches nothing; a stored parent slug would remove the guesswork
- Keep articles current *(proposed)* – several articles quote release status (Fedora, Ubuntu, Alpine, Tails, Solus, Slackware 16); review them each quarter
- Code style gate *(proposed)* – add a `pint.json` and format the existing code so `pint --test` can join `scripts/check` (34 files fail the default preset today)
- Feature tests for the page-to-distribution link *(proposed)* – the feature migrations are MySQL-only (FULLTEXT), so these need a MySQL test database
- Feature tests for the wiki, search and auth flows *(proposed)* – the `tests/Feature` and `tests/Unit/Features` folders are almost empty
- Server-side table of contents *(proposed)* – move heading extraction out of the client script if pages need SEO or no-JS support
- Sort direction indicators in the distro comparison table

---

## 🟢 Low Priority (Nice‑to‑Have)
- Syntax highlighting for code blocks in articles
- Command palette (⌘K) that searches pages and jumps to routes

---

## 🔭 Long‑Term Vision
- Per-distro landing pages that combine the infobox, family-tree position and related wiki pages
