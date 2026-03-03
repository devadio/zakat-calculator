=== Zakat Ultimate ===
Contributors: Abdullah HA - devad.me
Tags: zakat, ramadan, zakat al fitr, fidya, kaffarah, islamic, calculator
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.0
Stable tag: 3.2.0
License: GPL v2 or later

Lightweight Ramadan calculator for Zakat al-Maal, Zakat al-Fitr, Fidya, and Kaffarah.

== Description ==

**Zakat Ultimate** is a compact, fixed-size WordPress calculator focused on top Ramadan use cases.

**Shortcodes**

1. `[zakat_ultimate]`
   - Full calculator
   - No final CTA headline block

2. `[zakat_ultimate_pro]`
   - Full calculator
   - Final CTA headline block in result card

**Default Mode Control**

You can set the default opened calculator mode using shortcode attribute `default`:

- `default="1"` or `default="fitr"` -> Zakat al-Fitr
- `default="2"` or `default="fk"` -> Fidya / Kaffarah
- `default="3"` or `default="maal"` -> Zakat al-Maal

Examples:
- `[zakat_ultimate default="1"]`
- `[zakat_ultimate_pro default="fk"]`

**Calculators Included**

1. **Zakat al-Maal**
   - Multi-select assets: Cash, Gold, Silver
   - Dynamic Nisab check (Gold 85g / Silver 595g)
   - Zakat formula: `2.5%` after Nisab eligibility
   - No debt input in this version

2. **Zakat al-Fitr**
   - Number of persons + average amount per person
   - One-line formula summary in results

3. **Fidya / Kaffarah**
   - Single-choice mode:
     - Fidya (sick/elderly)
     - Kaffarah (Intentional fast-break)
   - Missed days, amount per day, number of persons
   - One-line formula summary in results

**UX Features**

- Fixed-size compact card (stable UI)
- Top-corner language switcher (default English)
- Help popups for Fidya and Kaffarah guidance
- Inline validation with clean error messages
- Works as a single lightweight PHP file (no build step)

== Installation ==

1. Upload `zakat-ultimate` to `/wp-content/plugins/`
2. Activate from the WordPress Plugins page
3. Insert shortcode in a page/post:
   - `[zakat_ultimate]`
   - `[zakat_ultimate_pro]`

== Frequently Asked Questions ==

= Which shortcode should I use? =
- Use `[zakat_ultimate]` for the calculator only.
- Use `[zakat_ultimate_pro]` to show the final CTA headline block in results.

= Can I choose which calculator opens by default? =
Yes. Use shortcode attribute `default` with `1/2/3` (or `fitr/fk/maal`).

= Does this plugin support multiple instances on one page? =
Yes. Each rendered calculator instance is scoped independently.

= Is this calculator mobile-friendly? =
Yes. The card remains compact and fixed-size with responsive layout rules.

== Changelog ==

= 3.2.0 =
* Ramadan v1 refactor
* Removed language selection step
* Added top-corner language switcher
* Reduced scope to Zakat al-Maal, Zakat al-Fitr, Fidya, and Kaffarah
* Added pro-only CTA headline block in final results
* Improved English copy, labels, and formula summaries

= 3.1.0 =
* Previous multi-step calculator release

== Upgrade Notice ==

= 3.2.0 =
This is a focused Ramadan refactor with cleaner UX and fewer, higher-priority calculator modes.
