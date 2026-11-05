Kudino is a store that sells fertilizer to people who grow things: farmers, orchardists, and people with houseplants. The design system is called **Field Almanac**: paper and ink, the analysis printed on the bag, and a fertilizing calendar. The goal is that the site reads like a reliable agronomy reference, not a generic shop template.

## Content fundamentals

- **Voice:** an experienced agronomist talking to a grower. Short, plain and specific. Address the reader as «تو» in calls to action and introductions («بگو چه می‌کاری»); use «شما» in forms and legal text.
- **Specific instead of general:** «۲ تا ۳ گرم در هر لیتر آب» rather than «مصرف آسان». Every claim carries a number, a month or an element.
- **Numbers:** prices, dates and counts use Persian digits (`kudino_fa()`), with the «٬» thousands separator and «تومان» after the number. Chemical analyses, formulas and SKUs use Latin mono digits (`20-20-20`, P<sub>2</sub>O<sub>5</sub>), because that is what is printed on the bag.
- **Dates:** always Jalali (`kudino_today()`). The top strip states this month's job («کار این ماه»).
- **No:** emoji, exclamation marks, invented statistics, or «بهترین» and «شگفت‌انگیز».
- Real copy samples: «هر خاک، کودِ خودش را می‌خواهد.» / «برگ‌ها خبر می‌دهند.» / «نسخه‌ی کود، پیش از خرید.»

## Visual foundations

### Colour
- The ground is always `kd-paper`. Recessed areas (image wells, the slip band, the current month) use `kd-paper-2`. Anything you write or print on (inputs, the label, the slip) uses `kd-sheet`.
- Text is `kd-ink`, then `kd-ink-2` for ledes and `kd-ink-3` for metadata. All three pass WCAG AA on `kd-paper` and `kd-paper-2`.
- **There is exactly one brand colour:** `kd-field`, for primary buttons, links, the active tab underline and focus.
- `kd-n`, `kd-p`, `kd-k`, `kd-o` and `kd-m` are **data only**: analysis bars, almanac marks, element table bands, the footer strata. Never use them as backgrounds, decoration or text (`kd-p` especially, at 3.5:1).
- No gradients except the footer strata, which are three flat bands.

### Typography
- **Markazi Text** (an editorial Naskh by an Iranian designer) for headlines: `hero`, `h1`, `h2`, `h3`, `index`. Weight 600 throughout.
- **IBM Plex Sans Arabic** for the interface and running text: `lead`, `body`, `small`, `caption`, `price`.
- **IBM Plex Mono** only for analyses, chemical symbols, SKUs and atomic numbers: `label-code`, `element`, `code`.
- All three are self-hosted (OFL) so they load in Iran too. Body leading is 1.85, because Persian script needs room.

### Structure and layout
- **Rules, not boxes.** Every section opens with a title above a 1px `kd-ink` rule (`kd-head`). Rows and cells are separated by `kd-rule` hairlines. Cards with background and shadow do not exist.
- Asymmetric layout: a 4/8 split (title and lede on the start side, content on the end side) or 7/5 in the hero. Not every section is centred.
- Each section has its own structure: an index (categories), a periodic table (elements), a ruled shelf (products), a table (calendar), two columns with a vertical rule (routes), and a slip (consult). Don't repeat a structure.
- Spacing on a 4px grid; vertical rhythm between sections is `--kd-section` (72–136px).

### Shape, depth, motion
- Radius: `kd-r-1` (2px) for tags and image wells, `kd-r-2` (6px) for buttons and fields. Nothing is rounder.
- Shadow: only `kd-pop`, for menus and the drawer.
- Motion is short (120–220ms) with `--kd-ease`: arrows move 3–4px, the tab underline slides in, a row moves inward. Everything turns off with `prefers-reduced-motion`.
- Focus: a 2px `kd-field` outline with a 2px offset.

### Imagery
- Use real product photos on `kd-paper-2`. Without a photo, use the flat pack drawing (Illustrations). Stock photos, 3D renders and illustrations of happy farmers are not allowed.

## Iconography
- Line icons at 1.75 stroke on a 24px grid (`kudino_icon()` in `inc/icons.php`), **only for functions**: search, cart, account, menu, close, arrow, plus, heart. Section headings, categories and promises have no icons; type carries them.
- No emoji.

## Implementation
- WordPress theme: the `kudino/` folder in the repository. Tokens live in `assets/css/tokens.css`, components in `assets/css/main.css`.
- These design system files are generated from the theme with `design-system/build.py`; change the theme first, then rebuild.
