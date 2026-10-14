<?php
/**
 * @package  PixVisualize
 */
namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * Registers the [pixvisualize] shortcode.
 *
 * The shortcode renders the exact same wrapper markup as the Gutenberg block
 * render.php (same class + data-* attributes), so the React view.js script
 * hydrates shortcode output exactly like block output.
 */
class ShortcodeController extends BaseController
{
	public function register()
	{
		if ( ! $this->activated( 'pixvisualize_shortcode_manager' ) ) {
			return;
		}

		add_shortcode( 'pixvisualize', array( $this, 'render' ) );
	}

	/**
	 * Render the comparison slider from shortcode attributes.
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string      Slider markup (same DOM as the block render.php).
	 */
	public function render( $atts )
	{
		$raw_atts = is_array( $atts ) ? $atts : array();
		$atts = shortcode_atts(
			array(
				'before'                => '',
				'after'                 => '',
				'position'              => 50,
				'before_label'          => 'Before',
				'after_label'           => 'After',
				'show_labels'           => 1,
				'orientation'           => 'horizontal',
				'height'                => 450,
				'ratio'                 => '16/9',
				'full_width'            => 0,
				'handle'                => 'circle',
				'svg'                   => '',
				'handle_size'           => 24,
				'handle_bg'             => '',
				'handle_arrow_color'    => '#666666',
				'handle_padding'        => 0,
				'label_position_before' => 'top-left',
				'label_position_after'  => 'top-right',
				'label_bg'              => '#00000080',
				'label_color'           => '#ffffff',
				'label_font_size'       => 12,
				'label_font_weight'     => 600,
				'label_padding_x'       => 10,
				'label_padding_y'       => 5,
				'label_border_radius'   => 4,
				'separator_thickness'   => 2,
				'separator_color'       => '#ffffff',
				'separator_shadow'      => 1,
				'hover'                 => 0,
				'autoscan'              => 0,
				'autoscan_duration'     => 2500,
				'magnifier'             => 0,
				'magnifier_zoom'        => 2,
				'tablet_position'       => 50,
				'mobile_position'       => 50,
				'tablet_ratio'          => '',
				'mobile_ratio'          => '',
				'alignment'             => 'center',
				'caption_title'         => '',
				'caption_text'          => '',
				'show_caption'          => 1,
				'caption_bg'            => '#ffffff',
				'caption_title_color'   => '#1e293b',
				'caption_text_color'    => '#64748b',
				'caption_alignment'     => 'center',
				'caption_title_size'    => 22,
				'caption_text_size'     => 15,
				'caption_padding'       => 16,
				'caption_padding_x'     => 5,
				'caption_padding_y'     => 9,
				'container_shadow'              => 0,
				'container_shadow_color'        => '#000000',
				'container_shadow_horizontal'   => 0,
				'container_shadow_vertical'     => 8,
				'container_shadow_blur'         => 20,
				'container_shadow_spread'       => 0,
				'container_shadow_opacity'      => 35,
				'container_border_width'        => 0,
				'container_border'              => 0,
				'container_rounded'             => 0,
				'container_border_color'        => '#000000',
				'container_border_style'        => 'solid',
				'container_border_radius_tl'    => 0,
				'container_border_radius_tr'    => 0,
				'container_border_radius_br'    => 0,
				'container_border_radius_bl'    => 0,
			),
			$atts,
			'pixvisualize'
		);

		$atts = $this->mergeCaptionPadding( $raw_atts, $atts );

		$atts['svg'] = $this->decodeShortcodeSvg( isset( $atts['svg'] ) ? $atts['svg'] : '' );

		$this->enqueueViewAssets();

		return $this->buildSliderMarkup( $atts );
	}

	/**
	 * Merge user attributes with defaults, preserving the legacy uniform
	 * caption padding for old shortcodes.
	 *
	 * Old shortcodes only carry caption_padding (e.g. 30) without X/Y:
	 * in that case both axes inherit it instead of falling to 5/9, so
	 * existing content renders exactly as before.
	 *
	 * @param  array $raw User-supplied shortcode attributes.
	 * @return array      Merged attributes.
	 */
	private function mergeCaptionPadding( $raw, $merged )
	{
		if ( ! isset( $raw['caption_padding_x'] ) && isset( $raw['caption_padding'] ) ) {
			$merged['caption_padding_x'] = $merged['caption_padding'];
		}

		if ( ! isset( $raw['caption_padding_y'] ) && isset( $raw['caption_padding'] ) ) {
			$merged['caption_padding_y'] = $merged['caption_padding'];
		}

		return $merged;
	}

	/**
	 * Decode the SVG stored in the shortcode attribute.
	 *
	 * The generator now stores it fully entity-encoded (&lt; &gt; &amp;
	 * &quot;) so raw < > never reach post_content (they would be parsed
	 * as real HTML tags and corrupt the saved shortcode). Old shortcodes
	 * only had " encoded: decode twice to also repair those. Strip the
	 * XML prolog, DOCTYPE and potrace <metadata> (useless inline, they
	 * break dangerouslySetInnerHTML) and keep only <svg>...</svg>.
	 *
	 * @param  string $svg Raw shortcode attribute value.
	 * @return string      Clean inline SVG (or '').
	 */
	private function decodeShortcodeSvg( $svg )
	{
		if ( ! is_string( $svg ) || '' === trim( $svg ) ) {
			return '';
		}

		$decoded = html_entity_decode( $svg, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		if ( strpos( $decoded, '&quot;' ) !== false || strpos( $decoded, '&lt;' ) !== false || strpos( $decoded, '&amp;' ) !== false ) {
			$decoded = html_entity_decode( $decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}

		// Repair the_content filter damage (Gutenberg stores its SVG inside
		// an HTML comment and Elementor in postmeta: both bypass the filters,
		// the shortcode text does not). wpautop runs before do_shortcode and
		// injects <br />/<p> inside raw <svg> tags; a <br /> replaced a
		// newline (path separator) so it becomes a space again. Non-breaking
		// spaces are not valid SVG separators either.
		$decoded = preg_replace( '/<\s*br\s*\/?\s*>/i', ' ', $decoded );
		// NOTE: \b boundary is mandatory: /<\/?p[^>]*>/ would also eat
		// <path>, <polygon>, <polyline> and <pattern> tags.
		$decoded = preg_replace( '/<\/?p\b[^>]*>/i', '', $decoded );
		$decoded = str_replace( array( "\xC2\xA0", "\xA0" ), ' ', $decoded );

		$decoded = preg_replace( '/<\?xml[^?]*\?>\s*/', '', $decoded );
		$decoded = preg_replace( '/<!DOCTYPE[^>]*>\s*/', '', $decoded );
		$decoded = preg_replace( '/<metadata>[\s\S]*?<\/metadata>\s*/', '', $decoded );
		// Single-line data attribute (mirrors the JS generator).
		$decoded = preg_replace( '/\s*[\r\n]+\s*/', ' ', $decoded );
		// Force currentColor so the icon inherits Handle Arrows Color instead
		// of resolving var(--svg-fill) against the front theme.
		$decoded = preg_replace( '/var\(\s*--svg-fill\s*,\s*currentColor\s*\)/', 'currentColor', $decoded );
		// Drop width/height on the opening <svg>: only viewBox matters, CSS
		// forces 100% inside the handle circle.
		$decoded = preg_replace_callback(
			'/<svg([^>]*)>/i',
			function ( $matches ) {
				$attrs = preg_replace( '/\s+width\s*=\s*(?:"[^"]*"|\'[^\']*\')/i', '', $matches[1] );
				$attrs = preg_replace( '/\s+height\s*=\s*(?:"[^"]*"|\'[^\']*\')/i', '', $attrs );
				return '<svg' . $attrs . '>';
			},
			$decoded,
			1
		);
		$decoded = trim( $decoded );

		if ( preg_match( '/<svg[\s\S]*<\/svg>/i', $decoded, $m ) ) {
			$decoded = $m[0];
		}

		return $decoded;
	}
}