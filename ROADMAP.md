# Roadmap

This document outlines the future direction of Tuxpedia.
Items are organized by priority, not by timeline. Items marked *(proposed)* have not been confirmed by the author.

---

## ✅ Completed (Milestones Achieved)
- v0.1.0: wiki CRUD with Git versioning, distro comparison table, full-text search, D3 family tree, authentication, "changed since last visit" badges, terminal simulator
- UI redesign "Modern Docs": design tokens, dark mode, hero, article layout with table of contents, mobile navigation, themed D3 tree and data table

---

## 🔥 High Priority (Critical)
- Sanitise rendered Markdown *(proposed)* – `Str::markdown()` output is printed unescaped; user-authored HTML in a page could run script. Use `html_input: strip` (or a sanitiser) before it is shown.
- Visual verification pass *(proposed)* – screenshot every page in light and dark and refresh `screenshots/` and the README

---

## 🟡 Medium Priority (Important)
- Distro identity colours *(proposed)* – per-distro accent colour on cards, article headers and tree nodes (COA 3 from the UI options)
- Server-side table of contents *(proposed)* – move heading extraction out of the client script if pages need SEO or no-JS support
- Sort direction indicators in the distro comparison table

---

## 🟢 Low Priority (Nice‑to‑Have)
- Syntax highlighting for code blocks in articles
- Command palette (⌘K) that searches pages and jumps to routes

---

## 🔭 Long‑Term Vision
- Infobox sidebar on distro pages populated from the comparison data
