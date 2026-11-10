<?php
/**
 * Shared SVG sanitizer for the custom handle icon.
 *
 * The custom SVG is stored by the block, Elementor, shortcode and
 * WooCommerce editors, then injected into the DOM with
 * dangerouslySetInnerHTML on the front end. Every input path must pass
 * through this allowlist so a stored SVG can never carry scripts, event
 * handlers or javascript: URLs (stored XSS).
 *
 * @package PixVisualize
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pixvisualize_svg_allowed_tags' ) ) {
	/**
	 * Allowed SVG elements and attributes.
	 *
	 * Attribute names are lowercase on purpose: wp_kses() compares them
	 * case-insensitively and outputs them lowercased; the case-sensitive
	 * ones (viewBox, preserveAspectRatio) are restored afterwards.
	 *
	 * @return array
	 */
	function pixvisualize_svg_allowed_tags() {
		return array(
			'svg'      => array(
				'viewbox'             => true,
				'xmlns'               => true,
				'xmlns:xlink'         => true,
				'preserveaspectratio' => true,
				'width'               => true,
				'height'              => true,
				'fill'                => true,
				'stroke'              => true,
				'stroke-width'        => true,
				'role'                => true,
				'aria-hidden'         => true,
				'aria-label'          => true,
				'focusable'           => true,
				'class'               => true,
				'version'             => true,
			),
			'g'        => array(
				'fill'            => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'transform'       => true,
				'opacity'         => true,
				'fill-rule'       => true,
				'clip-rule'       => true,
				'clip-path'       => true,
				'class'           => true,
			),
			'path'     => array(
				'd'               => true,
				'fill'            => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-rule'       => true,
				'clip-rule'       => true,
				'opacity'         => true,
				'class'           => true,
			),
			'polygon'  => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'fill-rule'    => true,
				'opacity'      => true,
			),
			'polyline' => array(
				'points'       => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'opacity'      => true,
			),
			'circle'   => array(
				'cx'           => true,
				'cy'           => true,
				'r'            => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'opacity'      => true,
			),
			'ellipse'  => array(
				'cx'           => true,
				'cy'           => true,
				'rx'           => true,
				'ry'           => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'opacity'      => true,
			),
			'rect'     => array(
				'x'            => true,
				'y'            => true,
				'width'        => true,
				'height'       => true,
				'rx'           => true,
				'ry'           => true,
				'fill'         => true,
				'stroke'       => true,
				'stroke-width' => true,
				'opacity'      => true,
			),
			'line'     => array(
				'x1'             => true,
				'y1'             => true,
				'x2'             => true,
				'y2'             => true,
				'stroke'         => true,
				'stroke-width'   => true,
				'stroke-linecap' => true,
				'opacity'        => true,
			),
			'title'    => array(),
			'desc'     => array(),
		);
	}
}

if ( ! function_exists( 'pixvisualize_sanitize_svg' ) ) {
	/**
	 * Sanitize an inline SVG string down to a safe allowlist.
	 *
	 * @param mixed $svg Raw SVG markup.
	 * @return string Sanitized SVG (or an empty string).
	 */
	function pixvisualize_sanitize_svg( $svg ) {
		if ( ! is_string( $svg ) || '' === trim( $svg ) ) {
			return '';
		}

		if ( ! function_exists( 'wp_kses' ) ) {
			return '';
		}

		// Drop document-level and active content before the allowlist pass.
		$svg = preg_replace( '/<\?xml[^?]*\?>\s*/i', '', $svg );
		$svg = preg_replace( '/<!DOCTYPE[^>]*>\s*/i', '', $svg );
		$svg = preg_replace( '/<metadata\b[^>]*>[\s\S]*?<\/metadata>\s*/i', '', $svg );
		$svg = preg_replace( '/<script\b[^>]*>[\s\S]*?<\/script>\s*/i', '', $svg );
		$svg = preg_replace( '/<style\b[^>]*>[\s\S]*?<\/style>\s*/i', '', $svg );
		$svg = preg_replace( '/<foreignObject\b[^>]*>[\s\S]*?<\/foreignObject>\s*/i', '', $svg );

		$clean = wp_kses( $svg, pixvisualize_svg_allowed_tags() );

		// wp_kses() lower-cases attribute names; restore SVG's case-sensitive ones.
		$clean = str_replace(
			array( 'viewbox=', 'preserveaspectratio=' ),
			array( 'viewBox=', 'preserveAspectRatio=' ),
			$clean
		);

		if ( preg_match( '/<svg\b[\s\S]*<\/svg>/i', $clean, $matches ) ) {
			$clean = $matches[0];
		} else {
			return '';
		}

		// Defence in depth: drop event handlers and scripted URLs even if a
		// future allowlist edit re-introduces them.
		$clean = preg_replace( '/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean );
		$clean = preg_replace( '/\s(?:xlink:)?href\s*=\s*("|\')\s*(?:javascript|vbscript|data):[^"\']*\1/i', '', $clean );

		return trim( $clean );
	}
}
