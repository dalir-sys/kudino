#!/usr/bin/env python3
"""
Build the Kudino design system (Claude Design System artifact files) from the theme.

The theme is the single source of truth:
    kudino/assets/css/tokens.css  -> project/tokens.json
    kudino/assets/css/main.css    -> project/components/bundle.css
    the rendered homepage HTML    -> project/components/<Name>/preview.html
                                     project/assets/{Logos,Illustrations}/*.svg

Usage (with the theme active on a local WordPress):
    python3 design-system/build.py http://localhost:8080/
"""
import datetime
import json
import pathlib
import re
import sys
import urllib.request

ROOT = pathlib.Path(__file__).resolve().parent.parent
CSS = ROOT / "kudino" / "assets" / "css"
FONTS = ROOT / "kudino" / "assets" / "fonts"
OUT = ROOT / "design-system" / "project"

# ---------------------------------------------------------------- token notes
COLOR_USE = {
    "kd-paper": "Page ground. Everything sits on paper; there are no cards behind sections.",
    "kd-paper-2": "Recessed wells: product image wells, the prescription band, the current-month column, the next plan step.",
    "kd-paper-3": "Pressed / disabled fills, the empty cart count.",
    "kd-sheet": "The things you write on or print on: inputs, the analysis label, the prescription slip, menus.",
    "kd-ink": "Text and every structural rule (section heads, label borders, calendar frame). 15.9:1 on kd-paper.",
    "kd-ink-2": "Secondary text: ledes, table labels. 8.4:1 on kd-paper, 7.6:1 on kd-paper-2.",
    "kd-ink-3": "Metadata, captions, counts. 5.7:1 on kd-paper, 5.2:1 on kd-paper-2, 4.5:1 on kd-paper-3.",
    "kd-rule": "Hairlines between rows and cells. Decorative separation only (1.4:1) — never the only cue for a control.",
    "kd-field": "The one brand colour: primary buttons, links, the selected tab underline, focus outline. Paper text on it is 9:1.",
    "kd-field-2": "Hover / pressed state of kd-field.",
    "kd-field-3": "Quiet selection: the active crop row in the almanac.",
    "kd-n": "DATA ONLY — nitrogen in analysis bars, element table and almanac marks. White text on it 5.1:1.",
    "kd-p": "DATA ONLY — phosphorus. 3.5:1 on paper: use as a mark or bar, never as text on paper.",
    "kd-k": "DATA ONLY — potassium; also the accent digit on the 404 page. 5.7:1 on paper.",
    "kd-o": "DATA ONLY — organic matter / humus marks in the almanac; footer strata.",
    "kd-m": "DATA ONLY — micronutrients (Fe, Zn, Ca, B, Mn, S, Mg) marks.",
    "kd-ok": "In-stock dot. Always next to a word.",
    "kd-warn": "Low-stock text and dot («فقط ۳ عدد مانده»). 5.5:1 on paper.",
    "kd-err": "Field errors and error notices. 6:1 on paper.",
}
SPACE_USE = {
    "kd-s-1": "Tag inner gaps.", "kd-s-2": "Icon to label.", "kd-s-3": "Compact list padding.",
    "kd-s-4": "Default control padding; mobile shelf cell padding.", "kd-s-5": "Shelf cell padding (desktop).",
    "kd-s-6": "Gap between tabs; promise padding.", "kd-s-8": "Head rule to content; slip padding.",
    "kd-s-10": "Column gap of split layouts; route padding.", "kd-s-12": "Between header and page body.",
    "kd-s-16": "Hero top padding; gap between hero columns.", "kd-s-24": "Largest vertical pause.",
}
RADIUS_USE = {
    "kd-r-1": "Tags, image wells, calendar marks — nearly square on purpose.",
    "kd-r-2": "Buttons, inputs, menus.",
    "kd-r-3": "Reserved for overlays (drawer). Nothing larger exists in the system.",
}


def css_vars(text):
    root = text[text.index(":root"):]
    return re.findall(r"--([a-z0-9-]+):\s*([^;]+);", root)


def build_tokens():
    pairs = css_vars((CSS / "tokens.css").read_text(encoding="utf-8"))
    color, space, radius, shadow, derived = [], [], [], [], []
    for name, val in pairs:
        val = val.strip()
        if name in COLOR_USE:
            color.append({"name": name, "value": val, "usage": COLOR_USE[name]})
        elif re.fullmatch(r"kd-s-\d+", name):
            space.append({"name": name, "value": val, "usage": SPACE_USE.get(name, "")})
        elif re.fullmatch(r"kd-r-\d", name):
            radius.append({"name": name, "value": val, "usage": RADIUS_USE.get(name, "")})
        elif name == "kd-pop":
            shadow.append({"name": name, "value": val, "usage": "The only shadow: menus and the drawer, things that float above the page."})
        else:
            derived.append((name, val))

    fonts = [
        {"family": "Markazi Text", "file": "fonts/markazi-text-arabic-wght-normal.woff2", "weight": "400 700"},
        {"family": "IBM Plex Sans Arabic", "file": "fonts/ibm-plex-sans-arabic-arabic-400-normal.woff2", "weight": "400"},
        {"family": "IBM Plex Sans Arabic", "file": "fonts/ibm-plex-sans-arabic-arabic-500-normal.woff2", "weight": "500"},
        {"family": "IBM Plex Sans Arabic", "file": "fonts/ibm-plex-sans-arabic-arabic-600-normal.woff2", "weight": "600"},
        {"family": "IBM Plex Sans Arabic", "file": "fonts/ibm-plex-sans-arabic-arabic-700-normal.woff2", "weight": "700"},
        {"family": "IBM Plex Mono", "file": "fonts/ibm-plex-mono-latin-400-normal.woff2", "weight": "400"},
        {"family": "IBM Plex Mono", "file": "fonts/ibm-plex-mono-latin-500-normal.woff2", "weight": "500"},
    ]
    t = {
        "fonts": fonts,
        "families": {
            "display": '"Markazi Text", "Noto Naskh Arabic", serif',
            "ui": '"IBM Plex Sans Arabic", Tahoma, system-ui, sans-serif',
            "mono": '"IBM Plex Mono", ui-monospace, Menlo, monospace',
        },
        "groups": [
            {"name": "Display — Markazi Text", "family": "display", "styles": [
                {"name": "hero", "fontSize": "88px", "lineHeight": 1.02, "fontWeight": 600, "sample": "هر خاک، کودِ خودش را می‌خواهد.", "usage": "Homepage headline only. One per site."},
                {"name": "h1", "fontSize": "56px", "lineHeight": 1.1, "fontWeight": 600, "sample": "نسخه‌ی کود، پیش از خرید.", "usage": "Page titles."},
                {"name": "h2", "fontSize": "46px", "lineHeight": 1.15, "fontWeight": 600, "sample": "تقویم کوددهی", "usage": "Section titles, always above a 1px kd-ink rule."},
                {"name": "h3", "fontSize": "30px", "lineHeight": 1.2, "fontWeight": 600, "sample": "باغ میوه", "usage": "Panel titles, the analysis label name."},
                {"name": "index", "fontSize": "28px", "lineHeight": 1.2, "fontWeight": 600, "sample": "کودهای آلی و دامی", "usage": "Rows of the category index; crop names in the almanac (22px)."},
            ]},
            {"name": "Analysis — IBM Plex Mono", "family": "mono", "styles": [
                {"name": "label-code", "fontSize": "72px", "lineHeight": 0.9, "fontWeight": 500, "letterSpacing": "-0.06em", "sample": "20-20-20", "usage": "The guaranteed analysis on the hero label. Latin digits on purpose: it is what is printed on the bag."},
                {"name": "element", "fontSize": "44px", "lineHeight": 1, "fontWeight": 500, "letterSpacing": "-0.04em", "sample": "Fe", "usage": "Chemical symbols in the element table."},
                {"name": "code", "fontSize": "13px", "lineHeight": 1.4, "fontWeight": 500, "sample": "10-52-10", "usage": "N-P-K on shelf items, SKUs, atomic numbers."},
            ]},
            {"name": "Interface — IBM Plex Sans Arabic", "family": "ui", "styles": [
                {"name": "lead", "fontSize": "18px", "lineHeight": 1.85, "fontWeight": 400, "sample": "بگویید چه می‌کارید؛ کود و زمان مصرفش را ما پیشنهاد می‌دهیم.", "usage": "Ledes under section titles, max 48ch."},
                {"name": "body", "fontSize": "15px", "lineHeight": 1.85, "fontWeight": 400, "sample": "کودهای آلی کند اثر می‌کنند اما بافت خاک را می‌سازند.", "usage": "Default text."},
                {"name": "item-title", "fontSize": "15px", "lineHeight": 1.6, "fontWeight": 600, "sample": "کود فسفر بالا ۱۰-۵۲-۱۰ برای گلدهی", "usage": "Product names on the shelf (max two lines)."},
                {"name": "small", "fontSize": "13px", "lineHeight": 1.7, "fontWeight": 500, "sample": "پیشنهاد کارشناس برای آبان", "usage": "Nav, tabs, notes."},
                {"name": "caption", "fontSize": "12px", "lineHeight": 1.6, "fontWeight": 500, "sample": "شیمیایی · ۱ کیلوگرم", "usage": "Metadata, field labels (600), kickers."},
                {"name": "price", "fontSize": "17px", "lineHeight": 1.3, "fontWeight": 700, "sample": "۲۸۵٬۰۰۰ تومان", "usage": "Prices: Persian digits, Persian thousands separator, 'تومان' at 11px kd-ink-3."},
            ]},
        ],
    }
    data = {
        "name": "Kudino",
        "version": 2,
        "color": {"themes": [{"id": "light", "name": "Paper"}], "tokens": color},
        "type": t,
        "spacing": {"tokens": space},
        "radius": {"tokens": radius},
        "shadow": {"tokens": shadow},
    }
    (OUT / "tokens.json").write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

    # bundle.css = font faces (paths relative to components/) + derived vars + components.
    tokens_css = (CSS / "tokens.css").read_text(encoding="utf-8")
    faces = "\n".join(l.replace("../fonts/", "../fonts/") for l in tokens_css.splitlines() if l.startswith("@font-face"))
    lines = ["/* Kudino components — generated from kudino/assets/css by design-system/build.py */", faces, ":root {"]
    for name, val in derived:
        lines.append("\t--%s: %s;" % (name, val))
    lines.append("}")
    main_css = (CSS / "main.css").read_text(encoding="utf-8")
    (OUT / "components").mkdir(parents=True, exist_ok=True)
    (OUT / "components" / "bundle.css").write_text("\n".join(lines) + "\n\n" + main_css, encoding="utf-8")
    return len(color), len(space), len(radius)


# ---------------------------------------------------------------- previews from the live page
VOID = {"img", "input", "br", "hr", "meta", "link", "source", "path", "rect", "circle", "line", "ellipse"}


def extract(html, start_marker):
    """Return the element that starts at `start_marker`, balancing its tag name."""
    i = html.index(start_marker)
    tag = re.match(r"<([a-zA-Z0-9]+)", html[i:]).group(1)
    depth, pos = 0, i
    pat = re.compile(r"<(/?)%s\b[^>]*?(/?)>" % tag)
    for m in pat.finditer(html, i):
        if m.group(1):
            depth -= 1
        elif not m.group(2):
            depth += 1
        if depth == 0:
            return html[i:m.end()]
    raise ValueError(start_marker)


def preview(name, marker, body, style=""):
    d = OUT / "components" / name
    d.mkdir(parents=True, exist_ok=True)
    doc = (marker + "\n<!doctype html>\n<html lang=\"fa\" dir=\"rtl\">\n<head><meta charset=\"utf-8\"><title>" + name +
           "</title>\n<style>html,body{margin:0;background:var(--kd-paper);color:var(--kd-ink);font-family:var(--kd-ui)} .pv{padding:24px;display:flex;flex-wrap:wrap;gap:16px;align-items:center}" +
           style + "</style></head>\n<body>\n<div class=\"pv\" dir=\"rtl\">\n" + body + "\n</div>\n</body>\n</html>\n")
    (d / "preview.html").write_text(doc, encoding="utf-8")


def build_previews(url):
    html = urllib.request.urlopen(url).read().decode("utf-8")
    html = re.sub(r'href="http://[^"]*"', 'href="#"', html)

    arrow = extract(html, '<svg class="kd-icon kd-icon--lead"')

    preview("Button", '<!-- @dsCard group="Actions" height=120 subtitle="field · ink · line · sizes · disabled" -->',
            '<a class="kd-btn kd-btn--lg" href="#">برنامه کوددهی' + arrow + '</a>'
            '<a class="kd-btn" href="#">افزودن به سبد</a>'
            '<a class="kd-btn kd-btn--ink" href="#">پرداخت</a>'
            '<a class="kd-btn kd-btn--line kd-btn--sm" href="#">برنامه اختصاصی مزرعه من</a>'
            '<button class="kd-btn" disabled>ثبت شد</button>'
            '<a class="kd-more" href="#">همه محصولات' + extract(html, '<svg class="kd-icon " width="16"') + '</a>')

    preview("Field", '<!-- @dsCard group="Forms" height=260 subtitle="boxed · error · ruled (slip) · search" -->',
            '<div class="kd-field" style="width:300px"><label class="kd-field__label" for="a">شماره موبایل</label><span class="kd-control"><input id="a" type="tel" placeholder="۰۹۱۲ ۰۰۰ ۰۰۰۰"></span><span class="kd-field__hint">برای تماس کارشناس</span></div>'
            '<div class="kd-field" style="width:300px"><label class="kd-field__label" for="b">ایمیل</label><span class="kd-control is-error"><input id="b" value="hello@kudino"></span><span class="kd-field__hint is-error">ایمیل کامل نیست؛ مثل name@mail.com بنویسید.</span></div>'
            '<div class="kd-field" style="width:300px"><label class="kd-field__label" for="c">سطح زیر کشت</label><span class="kd-control kd-control--ruled"><input id="c" placeholder="۲"><span class="kd-control__suffix">هکتار</span></span></div>'
            + extract(html, '<form class="kd-search"').replace('class="kd-search"', 'class="kd-search" style="width:300px"'),
            ".pv{align-items:start}")

    preview("Tabs", '<!-- @dsCard group="Forms" height=90 subtitle="underline tabs with counts — filter the shelf" -->',
            extract(html, '<div class="kd-tabs"'))

    preview("Tag", '<!-- @dsCard group="Status" height=100 subtitle="discount · label · warn · stock" -->',
            '<span class="kd-tag kd-tag--ink">۱۱٪ تخفیف</span><span class="kd-tag">ارگانیک</span><span class="kd-tag kd-tag--field">کالای هفته</span><span class="kd-tag kd-tag--warn">تاریخ مصرف نزدیک</span>'
            '<span style="flex-basis:100%"></span><span class="kd-stock">موجود</span><span class="kd-stock kd-stock--low">فقط ۳ عدد مانده</span><span class="kd-stock kd-stock--out">ناموجود</span>')

    npks = re.findall(r'<div class="kd-npk" role="img".*?</div>', html, re.S)
    preview("NpkAnalysis", '<!-- @dsCard group="Analysis" height=120 subtitle="code + proportional bar; zero shown as a stub" -->',
            "".join('<div style="width:200px">%s</div>' % n for n in npks[:4]))

    preview("AnalysisLabel", '<!-- @dsCard group="Analysis" height=720 subtitle="the hero product, typeset as the bag\'s guaranteed analysis" -->',
            '<div style="width:500px">' + extract(html, '<article class="kd-label">') + "</div>")

    shelf = extract(html, '<div class="kd-shelf"')
    items = re.findall(r'<article class="kd-item".*?</article>', shelf, re.S)
    preview("ShelfItem", '<!-- @dsCard group="Commerce" height=600 width=1100 subtitle="ruled shelf: discount, organic spec, low stock, new" -->',
            '<div class="kd-shelf" style="width:100%;margin:0">' + "".join(items[i] for i in (0, 3, 2, 5)) + "</div>", ".pv{display:block}")

    preview("CategoryIndex", '<!-- @dsCard group="Navigation" height=320 width=1100 subtitle="editorial index of fertilizer families" -->',
            '<div style="width:100%">' + extract(html, '<div class="kd-index">') + "</div>", ".pv{display:block}")

    preview("ElementTable", '<!-- @dsCard group="Navigation" height=380 width=900 subtitle="shop by deficiency sign; macro nutrients carry their data colour" -->',
            '<div style="width:100%">' + extract(html, '<div class="kd-elements">') + "</div>", ".pv{display:block}")

    preview("Almanac", '<!-- @dsCard group="Analysis" height=900 width=1280 subtitle="12-month fertilizing calendar; current month marked; one crop open" -->',
            '<div style="width:100%">' + extract(html, '<div class="kd-almanac">') + "</div>", ".pv{display:block}")

    preview("PrescriptionSlip", '<!-- @dsCard group="Forms" height=560 subtitle="consult request, set as a perforated slip with ruled fields" -->',
            '<div style="width:480px;padding:12px;background:var(--kd-paper-2)">' + extract(html, '<form class="kd-slip"') + "</div>")

    preview("TabBar", '<!-- @dsCard group="Navigation" height=110 subtitle="mobile tab bar (≤760px)" -->',
            extract(html, '<nav class="kd-tabbar"').replace('class="kd-tabbar"', 'class="kd-tabbar kd-tabbar--static" style="width:390px"'))

    # ---- assets: the mark and the pack drawings, taken from the same page
    logos = OUT / "assets" / "Logos"
    ill = OUT / "assets" / "Illustrations"
    logos.mkdir(parents=True, exist_ok=True)
    ill.mkdir(parents=True, exist_ok=True)
    mark = extract(html, '<svg class="kd-logo__mark"').replace('class="kd-logo__mark" ', 'xmlns="http://www.w3.org/2000/svg" width="104" height="104" ')
    (logos / "kudino-mark.svg").write_text(mark + "\n", encoding="utf-8")
    packs = re.findall(r'<svg viewBox="0 0 200 200".*?</svg>', shelf, re.S)
    for fname, idx in (("pack-pouch.svg", 0), ("pack-sack.svg", 1), ("pack-jug.svg", 2)):
        (ill / fname).write_text(packs[idx].replace("<svg ", '<svg width="400" height="400" ', 1) + "\n", encoding="utf-8")
    return len(list((OUT / "components").glob("*/preview.html")))


if __name__ == "__main__":
    c, s, r = build_tokens()
    print("tokens.json:", c, "colors,", s, "space,", r, "radii")
    if len(sys.argv) > 1:
        print("previews:", build_previews(sys.argv[1]))
    print("built", datetime.datetime.now().isoformat(timespec="seconds"))
