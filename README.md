# PixVisualize – Before After Image Comparison Slider

Showcase transformations with a fast, responsive Before/After slider for Gutenberg, Elementor, shortcodes and WooCommerce.

Free on WordPress.org. Pro available with flexible plans via secure Freemius checkout (see live pricing).

Product page: https://i-naridigit.com/products/pixvisualize/ — Support: https://i-naridigit.com/support/

PixVisualize is a complete and modern WordPress plugin to compare two images (Before/After) with an interactive sliding cursor, available in four environments:

* **Native Gutenberg block** — `create-block/pixvisualize`
* **Native Elementor widget** with visual controls and WYSIWYG preview
* **Shortcode** `[pixvisualize]` (generator built into the admin)
* **WooCommerce** — Before/After gallery on product pages

The rendering works both in the editor (back-office) and on the public site (front-office), with a smooth and fully responsive output.

---

## Pro Features

* **Advanced native Elementor widget**: full visual controls (content, layout, interactions) + WYSIWYG preview in the Elementor editor.
* **Custom handle**: circle, transparent, line, double arrow or custom SVG icon, with independent size, arrows color and padding.
* **Advanced labels**: position (4 corners), typography (size, weight), colors (background + text, alpha) and margins (padding, border-radius).
* **Cursor & handle**: one synchronized color for the bar and the circle, plus a grouped drop shadow with no seam.
* **Container style**: shadow (offset, blur, spread, opacity), border (width, style, color) and per-corner border-radius.
* **Vertical orientation**: Top/Bottom comparison in addition to horizontal.
* **Responsive per breakpoint**: distinct position and ratio for tablet and mobile.
* **Hover Mode**: the cursor follows the mouse pointer.
* **Intro animation (Auto-Scan)**: automatic sweep on load, configurable duration.
* **Magnifier Glass effect**: zoom (1.5x → 4x) at the cursor position.
* **Shortcode generation**: visual assistant in the admin to build `[pixvisualize ...]`, with a saved shortcode list and one-click reload of any shortcode for editing.
* **WooCommerce support**: product Before/After metabox, configurable placement and Product Data panel.

---

## Tech Stack

* **PHP 7.4+** / WordPress 6.8+ / WooCommerce 9.x (optional)
* **Modular OOP architecture** (Composer PSR-4, controllers, services)
* **React** (via `@wordpress/element`)
* **Gutenberg Block API** (`@wordpress/block-editor`, `@wordpress/components`)
* **Elementor API** (`Widget_Base`, `Controls_Manager`)
* **SASS / SCSS** (mixins and variables)

---

## What's inside

Gutenberg block, Elementor widget, shortcode generator (Pro) and WooCommerce gallery (Pro) — one renderer, one front-end engine, identical output everywhere.

---

## Installation

1. Download the `pixvisualize` folder into `/wp-content/plugins/`.
2. Activate the plugin via the **Plugins** menu.
3. Open the **PixVisualize** page in the admin menu: the 4 features (Block, Elementor, Shortcodes, WooCommerce) are active by default. Uncheck a toggle to deactivate the corresponding feature.
4. Add your comparison in the environment of your choice (see below).

> **Note:** The toggles are backward compatible: if the `pixvisualize` option is missing (migration, multisite), all features stay active by default.

### With the Gutenberg block

1. In the block editor, add the **PixVisualize** block.
2. Select your two images (Before / After).
3. Adjust the options in the sidebar (labels, position, ratio, orientation, handle, interactions…) and save.

### With the Elementor widget

If **Elementor** (free or Pro) is active and the corresponding toggle is checked, a **PixVisualize** widget is available in the *General* category.

1. Drag the **PixVisualize** widget into your layout.
2. In the settings panel, choose the **Before** and **After** images.
3. Configure content, layout, appearance and interactions; the preview is updated in real time (WYSIWYG).
4. Publish.

> The widget reuses the same server-side rendering and the same front-end script as the Gutenberg block: the public result is identical.

### With shortcodes

Two ways:

* **Visual generator**: *Shortcode Generator* tab of the **PixVisualize** page → select the images and the configuration, then copy the generated shortcode into your content. The tab has two sub-tabs: *Shortcode generator* and *Shortcode list* (saved shortcodes with rename, edit back into the generator, copy and delete; storage is per-browser via localStorage). A collapsed *Edit an existing shortcode* block reloads any pasted shortcode into the generator.
* **Legacy `handle_bg` attribute**: still read for backward compatibility (old shortcodes keep working) but no longer exposed in any UI; leave it empty so the handle follows `separator_color`.
* **Manual** — example:

```text
[pixvisualize before="https://example.com/before.jpg" after="https://example.com/after.jpg"
             position="50" ratio="16/9" orientation="horizontal" height="450"
             before_label="Before" after_label="After" handle="circle"]
```

Supported attributes (with their default values):

| Attribute | Default | Description |
| --- | --- | --- |
| `before` / `after` | `''` | Before / After image URLs (required) |
| `position` | `50` | Initial slider position (0–100) |
| `ratio` | `16/9` | Ratio: `21/9`, `2/1`, `16/9`, `3/2`, `4/3`, `1/1`, `3/4`, `2/3`, `9/16`, `1/2`, `9/21` |
| `orientation` | `horizontal` | `horizontal` or `vertical` |
| `height` | `450` | Height in px |
| `full_width` | `0` | `1` = full width |
| `before_label` / `after_label` | `Before` / `After` | Label texts |
| `show_labels` | `1` | `0` = hide the labels |
| `handle` | `circle` | `circle`, `transparent`, `line`, `arrows`, `custom` |
| `svg` | `''` | SVG code for the `custom` handle |
| `handle_size` | `24` | Handle circle diameter in px, independent control (16–64) |
| `handle_arrow_color` | `#666666` | Color of the arrows inside the handle, independent control |
| `handle_padding` | `0` | Inner spacing between the icon and the handle edge in px, independent control (0–20) |
| `label_position_before` / `label_position_after` | `top-left` / `top-right` | `top-left`, `top-right`, `bottom-left`, `bottom-right` |
| `label_bg` / `label_color` | `#00000080` / `#ffffff` | Label colors (alpha supported) |
| `label_font_size` / `label_font_weight` | `12` / `600` | Label typography |
| `label_padding_x` / `label_padding_y` / `label_border_radius` | `10` / `5` / `4` | Label margins |
| `separator_thickness` | `2` | Vertical bar thickness in px, independent control (1–10) |
| `separator_color` | `#ffffff` | Single synchronized control: background of the vertical bar and the handle circle |
| `separator_shadow` | `1` | Grouped drop shadow on the whole cursor + handle (`filter: drop-shadow()`, no seam at the junction) |
| `hover` | `0` | `1` = hover mode |
| `autoscan` / `autoscan_duration` | `0` / `2500` | Intro animation and duration (ms) |
| `magnifier` / `magnifier_zoom` | `0` / `2` | Magnifier effect and zoom |
| `tablet_position` / `mobile_position` | `50` / `50` | Position per breakpoint |
| `tablet_ratio` / `mobile_ratio` | `''` | Ratio per breakpoint (`''` = inherits the desktop ratio) |
| `alignment` | `center` | `left`, `center`, `right` |
| `container_shadow` | `0` | `1` = enable container drop shadow |
| `container_shadow_color` | `#000000` | Shadow color (hex) |
| `container_shadow_horizontal` | `0` | Horizontal offset (px) |
| `container_shadow_vertical` | `8` | Vertical offset (px) |
| `container_shadow_blur` | `20` | Blur radius (px) |
| `container_shadow_spread` | `0` | Spread radius (px) |
| `container_shadow_opacity` | `35` | Shadow opacity (0–100) |
| `container_border_width` | `0` | Border width (px, `0` = no border) |
| `container_border_color` | `#000000` | Border color (hex) |
| `container_border_style` | `solid` | `solid`, `dashed`, `dotted`, `double` |
| `container_border_radius_tl` | `0` | Top-left corner radius (px) |
| `container_border_radius_tr` | `0` | Top-right corner radius (px) |
| `container_border_radius_br` | `0` | Bottom-right corner radius (px) |
| `container_border_radius_bl` | `0` | Bottom-left corner radius (px) |
| `extra_class` | `''` | Additional CSS class |

### With WooCommerce

If **WooCommerce** is active and the corresponding toggle is checked, each product gets a **PixVisualize** metabox (preview + quick selection) and a **PixVisualize** tab in the Product Data panel.

1. Edit a product.
2. In the **PixVisualize** tab, select the Before/After images and the full configuration (ratio, orientation, position, placement, labels, container style, interactions).
3. Choose the **placement** of the comparison: above the gallery, in the summary, below the thumbnails, after the related products or in a dedicated product tab.
4. Save: the comparison is rendered on the product page with the same engine as the block.

---

## Activation / Deactivation

The **PixVisualize** page (admin menu) exposes 4 global toggles:

| Toggle | Manager | Effect |
| --- | --- | --- |
| Activate PixVisualize Block | `pixvisualize_block_manager` | Registers the Gutenberg block |
| Activate PixVisualize Elementor Widget | `pixvisualize_elementor_manager` | Registers the Elementor widget |
| Activate PixVisualize Shortcodes | `pixvisualize_shortcode_manager` | Enables `[pixvisualize]` + the generator |
| Activate PixVisualize WooCommerce Gallery | `pixvisualize_woocommerce_manager` | Enables the product gallery |

The settings of each instance (images, labels, position, ratio…) are stored in the post content (block) or in post meta (WooCommerce) and are **never** affected by this page.

### License

Licensing is handled by the native Freemius **Account** submenu (**PixVisualize → Account**): activate the key received by email (**Plugins → Activate License**), manage invoices and upgrades there. After a checkout, a success notice recalls these steps if Pro is not active yet.

## Free vs Pro

| | Free | Pro |
| --- | --- | --- |
| Gutenberg block + Elementor base widget | Yes | Yes |
| Labels, ratio, height, position, caption, container presets | Yes | Yes |
| Vertical mode, custom handle, advanced labels, separator | No | Yes |
| Responsive per breakpoint, hover, auto-scan, magnifier | No | Yes |
| Shortcode + generator, WooCommerce gallery | No | Yes |

Pro available with flexible plans. See live pricing, checkout, license and auto-updates via Freemius.

---

## FAQ

**Does the block work on mobile?**
Yes. The slider is fully responsive, supports touch swiping (*swipe*) and has dedicated positions/ratios per breakpoint (tablet, mobile). Touch behavior preserves page scroll: in horizontal mode a vertical swipe scrolls the page and a horizontal swipe moves the cursor (drag from anywhere); in vertical mode touching the image scrolls the page and the cursor is dragged from the handle only (enlarged 44px touch zone).

**Are my block settings affected by the settings page?**
No. Each block's settings (images, labels, position, ratio) are saved by Gutenberg in the post content. The settings page only controls the global activation of the features.

**What happens if I deactivate a toggle?**
The corresponding feature is no longer registered: the block disappears from the editor, and the Elementor widget, shortcode or product gallery are no longer rendered. Existing content stays intact (re-enabling the toggle brings it back).

**Can I use the shortcode without Elementor?**
Yes. The shortcode and the block work without Elementor; Elementor is only required for the widget.

**Does PixVisualize work with WooCommerce?**
Yes, if WooCommerce is active and the dedicated toggle is checked. Each product can display a Before/After comparison wherever you want.

**Can I migrate from the PixVisualize free plugin?**
Existing blocks are preserved (same block name `create-block/pixvisualize`). Upgrade without rebuild. Do not run Free and Pro simultaneously.

---

## Support

* Product page: https://i-naridigit.com/products/pixvisualize/
* Support: https://i-naridigit.com/support/
* License: PixVisualize → Account (native Freemius account, invoices, upgrades).

---

## Changelog

See `readme.txt` (`== Changelog ==`) — single source of truth, also displayed on WordPress.org.

## License

GPLv2 or later. See `LICENSE` (https://www.gnu.org/licenses/gpl-2.0.html).
