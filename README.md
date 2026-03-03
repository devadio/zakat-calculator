=== Zakat Ultimate ===
Contributors: Custom Plugin
Tags: zakat, sadaqah, charity, islamic, calculator, i18n
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 3.0.0
License: GPL v2 or later

Ultimate Zakat & Sadaqah calculator with correct Islamic calculations and two shortcode options.

== Description ==

**Zakat Ultimate** is a production-ready WordPress plugin for calculating Zakat and Sadaqah with correct Islamic jurisprudence.

**Two Shortcodes:**

1. `[zakat_ultimate]` - Standard version
   - All calculator features
   - No CTA/donation button
   
2. `[zakat_ultimate_pro]` - Pro version with CTA
   - All calculator features
   - Donation CTA button linking to https://lph.elvefa.org/

**Key Features:**

### Zakat Types (10)
- **Cash & Savings (Maal)** - Gold/Silver/Cash with material dropdown
- **Income & Salary** - Monthly calculations
- **Zakat Al-Fitr** - Per person calculation
- **Rental Property** - Annual rental income
- **Investments** - Stocks/bonds with Nisab check
- **Pension** - Retirement savings
- **Jewelry** - Gold/Silver with purity selection
- **Business** - Trade goods inventory
- **Agriculture** - Produce (10% rate)
- **Livestock** - Animals (40+ threshold)

### Sadaqah Types (4)
- **General Sadaqah** - Any amount
- **Sadaqah Jariyah** - Ongoing charity (wells, mosques)
- **Education Support** - Students sponsorship
- **Medical Aid** - Patient support

**Sadaqah Features:**
- Beneficiary type dropdown (Orphans, Families, Refugees, Poor, Students, Patients)
- Frequency selection (One-time, Monthly, Quarterly)
- Quantity input
- Amount per person

### 11 Languages
🇬🇧 English | 🇸🇦 Arabic (RTL) | 🇮🇩 Indonesian | 🇹🇷 Turkish | 🇵🇰 Urdu (RTL) | 🇩🇪 German | 🇪🇸 Spanish | 🇫🇷 French | 🇮🇹 Italian | 🇵🇹 Portuguese

### User Experience
✅ **Step-by-step wizard** (5 steps with progress indicator)
✅ **Help tooltips** on each field (? icons)
✅ **Back button** preserves selections
✅ **Print results** button
✅ **Responsive design** (mobile-first)
✅ **RTL support** for Arabic/Urdu

### Correct Islamic Calculations
✅ **Nisab:** Lower of Gold (85g) or Silver (595g)
✅ **Zakat Rate:** 2.5% on eligible wealth
✅ **Debts:** Deducted from zakatable wealth
✅ **Hawl:** One lunar year requirement noted
✅ **Different rates:** Agriculture (10%), Livestock (variable)

== Installation ==

1. Upload `zakat-ultimate` folder to `/wp-content/plugins/`
2. Activate the plugin through 'Plugins' menu
3. Use either shortcode on any page:
   - `[zakat_ultimate]` - Standard
   - `[zakat_ultimate_pro]` - With CTA

== Frequently Asked Questions ==

= Which shortcode should I use? =
- Use `[zakat_ultimate]` for a clean calculator without donation prompts
- Use `[zakat_ultimate_pro]` if you want to encourage donations via your link

= How accurate are the calculations? =
Calculations follow traditional Hanafi fiqh and are verified against established Zakat guidelines. Consult a scholar for complex cases.

= Can users go back and change selections? =
Yes! The back button preserves all selections and data.

= Is it mobile-friendly? =
Yes, designed mobile-first with touch-friendly interfaces.

= Can I change the CTA link in Pro version? =
Edit line 13 in the PHP file where `$cta_url` is defined.

= Does it support Right-to-Left languages? =
Yes, Arabic and Urdu are fully RTL supported.

== Changelog ==

= 3.0.0 =
* Initial release
* Two shortcode options
* 10 Zakat types with correct calculations
* 4 Sadaqah types with beneficiary/frequency
* Material dropdown for Jewelry
* Help tooltips throughout
* Back button state preservation
* Print results feature
* 11 languages

== Upgrade Notice ==

This is a complete rewrite with focus on:
- Correct Islamic calculations
- Excellent UX/UI/CX
- Lightweight single-file architecture
- No build step required

== Credits ==

Built with vanilla JavaScript and WordPress best practices.
