# NpkAnalysis

The signature component: the three numbers printed on the bag, plus a bar whose segment widths are proportional to N, P and K. PHP: `kudino_npk( array( 10, 52, 10 ) )`.

- The code uses Latin mono digits (`10-52-10`) on purpose: it is what is printed on the bag.
- Colours carry data: `kd-n`, `kd-p`, `kd-k`. Never use these colours for decoration.
- A zero renders as a 2px stub so you can see the element exists but is absent.
- A fertilizer without N-P-K gets a one-line `kd-item__spec` instead of this component.
