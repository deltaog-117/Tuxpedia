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
