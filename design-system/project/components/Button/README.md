# Button

A rectangular action with a 6px radius (`kd-r-2`) and a single fill colour. Provide: a verb as the label (`افزودن به سبد`, `برنامه کوددهی`) and `href` or `type`.

- `kd-btn`: `kd-field` fill with `kd-paper` text. At most one per region.
- `kd-btn--ink`: payment and final confirmation only.
- `kd-btn--line`: a secondary action next to a primary one; it fills with ink on hover.
- `kd-more`: an underlined text link to "see all", with an arrow that moves 3px.
- Heights: `--sm` 36px, default 44px, `--lg` 52px. Minimum touch target 44px.
- Don't: add a shadow, a gradient or a radius larger than `kd-r-2`. The label is never «کلیک کنید».
