# 📘 DIARY.md

## Architectural Decision Log

*A chronological record of decisions, trade-offs and reasoning. Git tracks what changed; this tracks why.*

---

## 📋 Decision Index

| Date | Decision Area | Choice | Status |
|------|---------------|--------|--------|
| 2026-09-19 | UI direction | Modern Docs (COA 2) | ✅ Confirmed |
| 2026-09-19 | Markdown safety | Strip HTML in `Content::toHtml()` | ✅ Confirmed |

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

