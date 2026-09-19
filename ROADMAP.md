# Roadmap

This document outlines the future direction of Tuxpedia.
Items are organized by priority, not by timeline. Items marked *(proposed)* have not been confirmed by the author.

---

## ✅ Completed (Milestones Achieved)
- v0.1.0: wiki CRUD with Git versioning, distro comparison table, full-text search, D3 family tree, authentication, "changed since last visit" badges, terminal simulator
- UI redesign "Modern Docs": design tokens, dark mode, hero, article layout with table of contents, mobile navigation, themed D3 tree and data table
- Markdown sanitisation: raw HTML is stripped and unsafe link schemes are dropped when a page is rendered (`Content::toHtml()`, covered by unit tests)

---

## 🔥 High Priority (Critical)
- **Distro identity: accent colours and infobox** – the "wow" feature (COA 3 from the UI options), built on top of the Modern Docs theme:
  - Give each distro a brand accent colour and use it on its wiki card, article header, comparison-table chip and family-tree node.
  - Add a Wikipedia-style infobox sidebar on distro pages (release model, package manager, default desktop, architecture, based on), filled from the comparison data.
  - Open question: how a wiki page is linked to a distribution record (matching slug, or an explicit column). This touches data and Blade, not just CSS.
- Visual verification pass *(proposed)* – screenshot every page in light and dark and refresh `screenshots/` and the README

---

## 🟡 Medium Priority (Important)
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
