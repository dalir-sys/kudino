# Almanac

A 12-month Jalali fertilizing calendar. Rows are crops, columns are months, and the marks are nutrient codes. Data comes from `kudino_crop_guides()`.

- The current month column has a light ground and a 3px ink bar on its header.
- Clicking a crop opens its plan, ordered from this month. The next step is marked «بعدی».
- On mobile the table scrolls horizontally inside its own frame, the crop column is sticky, and the current month is brought into view automatically.
- The hero finder form opens the same crop (without JS through `?crop=`).
