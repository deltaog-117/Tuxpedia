# 📘 DIARY.md

## Architectural Decision Log

*A chronological record of decisions, trade-offs and reasoning. Git tracks what changed; this tracks why.*

---

## 📋 Decision Index

| Date | Decision Area | Choice | Status |
|------|---------------|--------|--------|
| 2026-09-19 | UI direction | Modern Docs (COA 2) | ✅ Confirmed |

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
