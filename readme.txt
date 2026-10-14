=== PixVisualize ===
Contributors: inaridigit
Tags: before-after, image-comparison, gutenberg, elementor, woocommerce
Requires at least: 6.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.16
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Showcase transformations with a fast Before/After Image Comparison Slider for Gutenberg, Elementor, shortcodes and WooCommerce.

== Description ==

PixVisualize adds a professional Before/After image comparison slider to WordPress.

Use it with:

* **Gutenberg** – native block with live preview,
* **Elementor** – drag-and-drop widget with visual controls,
* **Shortcode** – `[pixvisualize]` embeddable anywhere (Pro),
* **WooCommerce** – Before/After gallery on product pages (Pro).

Built for agencies, photographers, renovation, beauty, automotive and e-commerce stores that need to *prove* the transformation.

**Free version includes:**

* Gutenberg block + Elementor base widget
* Custom Before/After labels, horizontal slider, aspect ratio, height, position
* Full width, alignment, caption title + text
* Container shadow, border and rounded corners (one-click presets)
* Touch, swipe and keyboard support, fully responsive

**Pro version unlocks:**

* Vertical orientation, custom handle (circle, line, arrows, custom SVG)
* Advanced labels, separator styling with grouped drop shadow
* Container fine-tuning: shadow, border, per-corner radius
* Responsive per breakpoint (tablet + mobile position and ratio)
* Hover mode, Auto-Scan intro animation, Magnifier loupe
* Shortcode generator with saved list + visual re-edit
* WooCommerce placements (14 locations) + Product Data panel
* Priority updates and support

A single codebase powers both versions. Upgrading keeps all your existing blocks and settings — no migration, no rebuild.

> Learn more: [PixVisualize product page](https://i-naridigit.com/products/pixvisualize/)
> Development: [GitHub repository](https://github.com/narisoa/pixvisualize-free)

== Installation ==

1. Install `pixvisualize` from **Plugins → Add New** or upload the ZIP via **Plugins → Add → Upload**.
2. Activate the plugin through the **Plugins** screen.
3. Open **PixVisualize** in the admin menu: features are active by default.
4. Add a comparison:
* **Gutenberg:** insert the **PixVisualize** block, pick Before/After images, publish.
* **Elementor:** drag the **PixVisualize** widget, configure, publish.
* **Shortcode (Pro):** PixVisualize → Shortcode Generator → copy, e.g. `[pixvisualize before="URL-BEFORE" after="URL-AFTER" position="50" ratio="16/9"]`
* **WooCommerce (Pro):** edit a product → **PixVisualize** tab → images + placement → Update.

== Frequently Asked Questions ==

= Does it work without Elementor or WooCommerce? =
Yes. The block and the base widget work standalone. Elementor is only required for the widget, WooCommerce only for the product gallery.

= Does the block work on mobile? =
Yes. Touch swipe, responsive positions/ratios per breakpoint, keyboard navigation (arrows, Home, End). Vertical swipe scrolls the page, horizontal swipe moves the slider.

= Will I lose content if I disable a feature or upgrade to Pro? =
No. Block settings live in post content, product settings in post meta. Disabling a toggle hides the feature; re-enabling restores it. Free → Pro upgrade keeps everything.

= What happens with a Pro shortcode on the Free version? =
The `[pixvisualize]` shortcode is a Pro feature and is not rendered in the Free version (standard WordPress behavior: shortcode shown as text). Upgrade to Pro to render it.

= Where do I manage my Pro license? =
PixVisualize → Account. After checkout, activate your key, then install `pixvisualize-pro.zip` when prompted. Support: [i-naridigit.com/support](https://i-naridigit.com/support/)

= What data does PixVisualize collect? =
Nothing without your consent. On activation, an opt-in screen (skippable) offers to share admin name/email, site URL, WordPress version and diagnostic data with Freemius for licensing, updates and support. No tracking otherwise.

== Screenshots ==

1. Before/After slider on the front-end (horizontal, circle handle).
2. Gutenberg sidebar: layout, appearance and interactions.
3. Elementor widget with live WYSIWYG preview.
4. Shortcode Generator (Pro) with saved list.
5. WooCommerce product tab with placement options (Pro).

== Upgrade Notice ==

= 1.0.16 =
Short descriptions now carry the full "Before/After Image Comparison Slider" phrase for directory search. No code change.

= 1.0.15 =
Short display name (PixVisualize) so the wordpress.org slug is pixvisualize. No code change.

= 1.0.14 =
Maintenance release (docs only, no code change): author credits unified under the i-NariDigit brand.

= 1.0.13 =
Maintenance release (docs only, no code change): fixes Tags for WordPress.org compliance (image comparison to image-comparison).

= 1.0.12 =
Maintenance release (docs only, no code change): Development link now points to the public Free mirror for WordPress.org review.

= 1.0.11 =
Maintenance release (docs only, no code change): public readme cleanup.

= 1.0.10 =
Maintenance release: WooCommerce image picker remove button is now a compact trash icon, so the long "Select Image" label never overflows its column.

= 1.0.9 =
Maintenance release: instant swatch preview in the shortcode generator, border transparency in Gutenberg, cleaner WooCommerce panels.

= 1.0.8 =
Recommended update: editor fidelity (column contain, working alignment preview, unclipped shadow) plus a leaner admin menu (License merged into native Account).

= 1.0.7 =
New: secondary "View Pro demo" button next to every Go Pro CTA (admin banner, Go Pro tab, Gutenberg and Elementor upsells); demo URL is centralized and filterable.

= 1.0.6 =
Maintenance release: same as 1.0.5 (PHPCS annotation hardened against the Freemius formatter).

= 1.0.5 =
Recommended update: WordPress.org compliance (output escaping, direct-access guards, Tested up to 7.1).

= 1.0.4 =
Recommended update: complete translations (French Canadian locale, automatic locale-variant fallback) plus a steadier shortcode generator preview.

= 1.0.3 =
Maintenance release. Recommended update: commercial docs cleanup plus WooCommerce placement hardening.

= 1.0.2 =
Maintenance release. Recommended update: fixes Pro activation when Free is still active.

== Changelog ==

= 1.0.16 =
* Short descriptions (header + readme) now carry the full "Before/After Image Comparison Slider" phrase for directory search. No functional change.

= 1.0.15 =
* Short display name (PixVisualize) so the wordpress.org slug is pixvisualize; readme title kept identical to the header. No functional change.

= 1.0.14 =
* Maintenance release (docs only, no functional change): author credits unified under the i-NariDigit brand (package/composer metadata).

= 1.0.13 =
* Maintenance release (docs only, no functional change): Tags fixed for WordPress.org guideline #12 (image-comparison with hyphen instead of space).

= 1.0.12 =
* Maintenance release (docs only, no functional change): Development link points to the public Free mirror (https://github.com/narisoa/pixvisualize-free) for WordPress.org guideline #4.

= 1.0.11 =
* Maintenance release (docs only, no functional change): public readme no longer references any internal dev file.

= 1.0.10 =
* WooCommerce image picker: the Remove button is now an icon-only trash button (dashicons-trash with title + aria-label), compact 30px so "Select Image" never overflows the 180px picker column. JS hook (`.pixvisualize-woo-remove` + `data-remove`) unchanged.

= 1.0.9 =
* Shortcode generator: preset swatches apply to the live preview instantly (was one pick behind).
* Gutenberg: container Border Color now supports transparency like everywhere else.
* WooCommerce product panel: collapsed sections use a single clean border (accent kept on the open section only).

= 1.0.8 =
* Removed the redundant License submenu (native Freemius Account already covers it); checkout notices now point to Account/Extensions.
* Gutenberg editor fidelity: block preview contained in narrow columns (same contain layout as the front), alignment preview (left/center/right) now works in the editor, container shadow no longer clipped in the editor.
* Upsell CTAs (Gutenberg panel, Elementor card): full-width stacked buttons, identical size whatever the label length.

= 1.0.7 =
* Secondary "View Pro demo" button next to every Go Pro CTA (admin banner, Go Pro tab, Gutenberg panel, Elementor card). Demo URL centralized in `PIXVISUALIZE_DEMO_URL` + `pixvisualize_get_demo_url()` filter (`pixvisualize_demo_url`).

= 1.0.6 =
* PHPCS annotation on render.php hardened (disable/enable block: the Freemius formatter detaches trailing ignores).

= 1.0.5 =
* WordPress.org Plugin Check compliance: escaped outputs (wp_kses/esc_url/esc_attr), ABSPATH guards on templates, translators comments, Tested up to 7.1, lean SVN payload (dev files pruned before deploy) and marker gate fixed.

= 1.0.4 =
* Translations completed (465 msgids, 7 locales): new fr_CA locale, automatic locale-variant fallback (fr_BE to fr_FR, etc.), missing UI strings wrapped (Gutenberg ratio option, editor placeholder, slider aria-label, image alt), front script translations loaded.
* Shortcode Generator: live preview no longer jumps on every keystroke (debounced preview while typing, guarded DOM writes).

= 1.0.3 =
* Commercial positioning: sales-oriented headers, comments and docs (no tutorial tone), live pricing via Freemius/product page only, leaner public README, internal dev notes separated from shipped files, WooCommerce placement hardening.


= 1.0.2 =
* Fix fatal "Cannot redeclare pixvisualize_activate()" when activating Pro while Free is active (official Freemius coexistence pattern).

= 1.0.1 =
* Pro features gated on premium code + active license. Persistent install-Pro notice until pixvisualize-pro.zip is installed.

= 1.0.0 =
* Initial release: Gutenberg block, Elementor widget, shortcode + generator, WooCommerce gallery, 4 global toggles, container shadow/border/radius.

== Upgrade to Pro ==

Go Pro to unlock vertical mode, custom handles, advanced labels, hover / auto-scan / magnifier, responsive per breakpoint, shortcode generator and WooCommerce gallery.

See current plans on the [PixVisualize product page](https://i-naridigit.com/products/pixvisualize/). Checkout, license and auto-updates are handled securely via Freemius.
