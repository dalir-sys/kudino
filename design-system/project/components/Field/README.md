# Field

An input with a real label. Provide: `kd-field__label`, a `kd-control` holding the `input`/`select`, and an optional `kd-field__hint`.

- Boxed (default): on a `kd-sheet` ground with a `kd-rule` border. On focus the border turns `kd-ink` and gets a 1px ring.
- `kd-control--ruled`: a line to write on. Used only on the prescription slip.
- Error: `.is-error` plus a hint that says what is wrong and how to fix it.
- A unit (hectare, kilogram) goes in `kd-control__suffix`, not in the placeholder.
