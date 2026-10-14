<?php
defined( 'ABSPATH' ) || exit;
/**
 * PixVisualize — Product Data panel (full comparator configuration).
 *
 * The panel mirrors the Gutenberg block inspector / Elementor widget
 * structure: "Images & Content", "Caption", "Layout & Orientation",
 * "Appearance" and "Interactions". A live CSS preview at the top is kept
 * in sync by assets/js/pixvisualize-woocommerce-metabox.js.
 *
 * @package  PixVisualize
 *
 * @var \WP_Post $post Current product post object.
 */

$before_id    = absint( get_post_meta( $post->ID, '_pixvisualize_before_image', true ) );
$after_id     = absint( get_post_meta( $post->ID, '_pixvisualize_after_image', true ) );
$before_url   = $before_id ? wp_get_attachment_image_url( $before_id, 'large' ) : '';
$after_url    = $after_id ? wp_get_attachment_image_url( $after_id, 'large' ) : '';

$ratio        = get_post_meta( $post->ID, '_pixvisualize_ratio', true );
$ratio        = $ratio ?: '16/9';
$orientation  = get_post_meta( $post->ID, '_pixvisualize_orientation', true );
$orientation  = $orientation ?: 'horizontal';
$height       = absint( get_post_meta( $post->ID, '_pixvisualize_height', true ) );
$height       = $height > 0 ? $height : 450;
$position_raw = get_post_meta( $post->ID, '_pixvisualize_position', true );
$position     = '' === $position_raw ? 50 : absint( $position_raw );
$before_label = get_post_meta( $post->ID, '_pixvisualize_before_label', true );
$before_label = '' !== $before_label ? $before_label : 'Before';
$after_label  = get_post_meta( $post->ID, '_pixvisualize_after_label', true );
$after_label  = '' !== $after_label ? $after_label : 'After';
$show_labels  = get_post_meta( $post->ID, '_pixvisualize_show_labels', true );
$full_width   = get_post_meta( $post->ID, '_pixvisualize_full_width', true );
$handle       = get_post_meta( $post->ID, '_pixvisualize_handle', true );
$handle       = $handle ?: 'circle';
$handle_svg   = get_post_meta( $post->ID, '_pixvisualize_handle_svg', true );
$handle_size  = get_post_meta( $post->ID, '_pixvisualize_handle_size', true );
$handle_size  = '' === $handle_size ? 24 : max( 16, min( 64, absint( $handle_size ) ) );
$handle_bg    = get_post_meta( $post->ID, '_pixvisualize_handle_background', true );
$handle_arrow_color = get_post_meta( $post->ID, '_pixvisualize_handle_arrow_color', true );
$handle_arrow_color = $handle_arrow_color ?: '#666666';
$handle_pad   = get_post_meta( $post->ID, '_pixvisualize_handle_padding', true );
$handle_pad   = '' === $handle_pad ? 0 : max( 0, min( 20, absint( $handle_pad ) ) );

$label_pos_before = get_post_meta( $post->ID, '_pixvisualize_label_position_before', true );
$label_pos_before = $label_pos_before ?: 'top-left';
$label_pos_after  = get_post_meta( $post->ID, '_pixvisualize_label_position_after', true );
$label_pos_after  = $label_pos_after ?: 'top-right';
$label_bg         = get_post_meta( $post->ID, '_pixvisualize_label_background', true );
$label_bg         = $label_bg ?: '#00000080';
$label_color      = get_post_meta( $post->ID, '_pixvisualize_label_color', true );
$label_color      = $label_color ?: '#ffffff';
$label_font_size  = absint( get_post_meta( $post->ID, '_pixvisualize_label_font_size', true ) );
$label_font_size  = $label_font_size ?: 12;
$label_font_weight = absint( get_post_meta( $post->ID, '_pixvisualize_label_font_weight', true ) );
$label_font_weight = $label_font_weight ?: 600;
$label_padding_x  = get_post_meta( $post->ID, '_pixvisualize_label_padding_x', true );
$label_padding_x  = '' === $label_padding_x ? 10 : absint( $label_padding_x );
$label_padding_y  = get_post_meta( $post->ID, '_pixvisualize_label_padding_y', true );
$label_padding_y  = '' === $label_padding_y ? 5 : absint( $label_padding_y );
$label_radius     = get_post_meta( $post->ID, '_pixvisualize_label_border_radius', true );
$label_radius     = '' === $label_radius ? 4 : absint( $label_radius );

$caption_title      = get_post_meta( $post->ID, '_pixvisualize_caption_title', true );
$caption_text       = get_post_meta( $post->ID, '_pixvisualize_caption_text', true );
$show_caption       = get_post_meta( $post->ID, '_pixvisualize_show_caption', true );
$show_caption       = '' === $show_caption ? '1' : $show_caption;
$caption_bg         = get_post_meta( $post->ID, '_pixvisualize_caption_bg', true );
$caption_bg         = $caption_bg ?: '#ffffff';
$caption_title_color = get_post_meta( $post->ID, '_pixvisualize_caption_title_color', true );
$caption_title_color = $caption_title_color ?: '#1e293b';
$caption_text_color = get_post_meta( $post->ID, '_pixvisualize_caption_text_color', true );
$caption_text_color = $caption_text_color ?: '#64748b';
$caption_alignment  = get_post_meta( $post->ID, '_pixvisualize_caption_alignment', true );
$caption_alignment  = in_array( $caption_alignment, array( 'left', 'center', 'right' ), true ) ? $caption_alignment : 'center';
$caption_title_size = get_post_meta( $post->ID, '_pixvisualize_caption_title_size', true );
$caption_title_size = '' === $caption_title_size ? 22 : absint( $caption_title_size );
$caption_text_size  = get_post_meta( $post->ID, '_pixvisualize_caption_text_size', true );
$caption_text_size  = '' === $caption_text_size ? 15 : absint( $caption_text_size );
$caption_padding    = get_post_meta( $post->ID, '_pixvisualize_caption_padding', true );
$has_legacy_padding = '' !== $caption_padding && false !== $caption_padding;
$caption_padding    = $has_legacy_padding ? absint( $caption_padding ) : 16;
$caption_padding_x  = get_post_meta( $post->ID, '_pixvisualize_caption_padding_x', true );
$caption_padding_x  = '' === $caption_padding_x ? ( $has_legacy_padding ? $caption_padding : 5 ) : absint( $caption_padding_x );
$caption_padding_y  = get_post_meta( $post->ID, '_pixvisualize_caption_padding_y', true );
$caption_padding_y  = '' === $caption_padding_y ? ( $has_legacy_padding ? $caption_padding : 9 ) : absint( $caption_padding_y );
$caption_visible    = '1' === $show_caption && ( '' !== trim( (string) $caption_title ) || '' !== trim( (string) $caption_text ) );

$separator_thickness = get_post_meta( $post->ID, '_pixvisualize_separator_thickness', true );
$separator_thickness = '' === $separator_thickness ? 2 : absint( $separator_thickness );
$separator_color = get_post_meta( $post->ID, '_pixvisualize_separator_color', true );
$separator_color = $separator_color ?: '#ffffff';
$separator_shadow = get_post_meta( $post->ID, '_pixvisualize_separator_shadow', true );

$container_shadow             = get_post_meta( $post->ID, '_pixvisualize_container_shadow', true );
$container_shadow_color       = get_post_meta( $post->ID, '_pixvisualize_container_shadow_color', true );
$container_shadow_color       = $container_shadow_color ?: '#000000';
$container_shadow_horizontal  = get_post_meta( $post->ID, '_pixvisualize_container_shadow_horizontal', true );
$container_shadow_horizontal  = '' === $container_shadow_horizontal ? 0 : absint( $container_shadow_horizontal );
$container_shadow_vertical    = get_post_meta( $post->ID, '_pixvisualize_container_shadow_vertical', true );
$container_shadow_vertical    = '' === $container_shadow_vertical ? 8 : absint( $container_shadow_vertical );
$container_shadow_blur        = get_post_meta( $post->ID, '_pixvisualize_container_shadow_blur', true );
$container_shadow_blur        = '' === $container_shadow_blur ? 20 : absint( $container_shadow_blur );
$container_shadow_spread      = get_post_meta( $post->ID, '_pixvisualize_container_shadow_spread', true );
$container_shadow_spread      = '' === $container_shadow_spread ? 0 : (int) $container_shadow_spread;
$container_shadow_opacity     = get_post_meta( $post->ID, '_pixvisualize_container_shadow_opacity', true );
$container_shadow_opacity     = '' === $container_shadow_opacity ? 35 : absint( $container_shadow_opacity );
$container_border_width       = get_post_meta( $post->ID, '_pixvisualize_container_border_width', true );
$container_border_width       = '' === $container_border_width ? 0 : absint( $container_border_width );
// FREE toggles (checkboxes): default 2px solid gray border, default 16px radius.
$container_border             = get_post_meta( $post->ID, '_pixvisualize_container_border', true );
$container_rounded            = get_post_meta( $post->ID, '_pixvisualize_container_rounded', true );
$container_border_color       = get_post_meta( $post->ID, '_pixvisualize_container_border_color', true );
$container_border_color       = $container_border_color ?: '#000000';
$container_border_style       = get_post_meta( $post->ID, '_pixvisualize_container_border_style', true );
$container_border_style       = $container_border_style ?: 'solid';
$container_border_radius_tl   = get_post_meta( $post->ID, '_pixvisualize_container_border_radius_tl', true );
$container_border_radius_tl   = '' === $container_border_radius_tl ? 0 : absint( $container_border_radius_tl );
$container_border_radius_tr   = get_post_meta( $post->ID, '_pixvisualize_container_border_radius_tr', true );
$container_border_radius_tr   = '' === $container_border_radius_tr ? 0 : absint( $container_border_radius_tr );
$container_border_radius_br   = get_post_meta( $post->ID, '_pixvisualize_container_border_radius_br', true );
$container_border_radius_br   = '' === $container_border_radius_br ? 0 : absint( $container_border_radius_br );
$container_border_radius_bl   = get_post_meta( $post->ID, '_pixvisualize_container_border_radius_bl', true );
$container_border_radius_bl   = '' === $container_border_radius_bl ? 0 : absint( $container_border_radius_bl );

$hover_mode          = get_post_meta( $post->ID, '_pixvisualize_hover_mode', true );
$auto_scan           = get_post_meta( $post->ID, '_pixvisualize_auto_scan', true );
$auto_scan_duration  = get_post_meta( $post->ID, '_pixvisualize_auto_scan_duration', true );
$auto_scan_duration  = $auto_scan_duration ?: 2500;
$magnifier           = get_post_meta( $post->ID, '_pixvisualize_magnifier', true );
$magnifier_zoom      = get_post_meta( $post->ID, '_pixvisualize_magnifier_zoom', true );
$magnifier_zoom      = $magnifier_zoom ?: 2;

$tablet_position = get_post_meta( $post->ID, '_pixvisualize_tablet_position', true );
$tablet_position = '' === $tablet_position ? 50 : absint( $tablet_position );
$mobile_position = get_post_meta( $post->ID, '_pixvisualize_mobile_position', true );
$mobile_position = '' === $mobile_position ? 50 : absint( $mobile_position );
$tablet_ratio    = get_post_meta( $post->ID, '_pixvisualize_tablet_ratio', true );
$mobile_ratio    = get_post_meta( $post->ID, '_pixvisualize_mobile_ratio', true );
$alignment       = get_post_meta( $post->ID, '_pixvisualize_alignment', true );
$alignment       = $alignment ?: 'center';

$placement = get_post_meta( $post->ID, '_pixvisualize_placement', true );

if ( ! in_array(
	$placement,
	array(
		'above_gallery',
		'above_main_image',
		'under_thumbnails',
		'summary_before_title',
		'summary_after_title',
		'summary_after_price',
		'summary_after_excerpt',
		'summary_after_cart',
		'summary_end',
		'after_summary_before_tabs',
		'after_summary_after_tabs',
		'after_summary_after_related',
		'after_single_product',
		'product_tab',
	),
	true
) ) {
	$placement = 'above_gallery';
}

/* Theme scan: placements the active theme cannot display are hidden from
   the select (kept in code, never deleted). Safety rules: the saved value
   always stays selectable (the theme may have changed since it was saved),
   and when nothing is detected at all (e.g. block theme) everything stays
   visible with a warning instead of an empty select. */
$unavailable_placements = isset( $unavailable_placements ) && is_array( $unavailable_placements ) ? $unavailable_placements : array();
$placement_scan_warning = count( $unavailable_placements ) >= 14;
if ( $placement_scan_warning ) {
	$unavailable_placements = array();
}
$show_placement = static function ( $value ) use ( $placement, $unavailable_placements ) {
	return $value === $placement || ! in_array( $value, $unavailable_placements, true );
};
$show_placement_group = static function ( $values ) use ( $show_placement ) {
	foreach ( $values as $value ) {
		if ( $show_placement( $value ) ) {
			return true;
		}
	}
	return false;
};
$placement_group_main    = array( 'above_gallery', 'above_main_image', 'under_thumbnails' );
$placement_group_summary = array( 'summary_before_title', 'summary_after_title', 'summary_after_price', 'summary_after_excerpt', 'summary_after_cart', 'summary_end' );
$placement_group_after   = array( 'after_summary_before_tabs', 'after_summary_after_tabs', 'after_summary_after_related' );
$hidden_placements       = array_values( array_diff( $unavailable_placements, array( $placement ) ) );
$saved_placement_missing = in_array( $placement, $unavailable_placements, true );

$tab_title       = get_post_meta( $post->ID, '_pixvisualize_tab_title', true );
$tab_title       = '' !== $tab_title ? $tab_title : 'Comparison';
$desc            = get_post_meta( $post->ID, '_pixvisualize_desc', true );
$desc_layout     = get_post_meta( $post->ID, '_pixvisualize_desc_layout', true );
$desc_layout     = $desc_layout ?: 'top';
$desc_valign     = get_post_meta( $post->ID, '_pixvisualize_desc_vertical', true );
$desc_valign     = in_array( $desc_valign, array( 'top', 'middle', 'bottom' ), true ) ? $desc_valign : 'middle';

/* Live preview geometry (mirrors the Elementor content_template). */
$ratio_parts    = explode( '/', $ratio );
$ratio_w        = isset( $ratio_parts[0] ) ? (float) $ratio_parts[0] : 16;
$ratio_h        = isset( $ratio_parts[1] ) ? (float) $ratio_parts[1] : 9;
$box_width      = $ratio_h > 0 ? (int) round( $height * $ratio_w / $ratio_h ) : 800;
// Contain layout: fit inside the parent (width AND fixed height) with the
// ratio respected — 21/9 fills the width, 9/21 fills the height.
$container_width  = 'min(100%, ' . $box_width . 'px)';
$container_margin = '0 auto';
$is_vertical      = 'vertical' === $orientation;
$handle_class     = 'pixvisualize-elementor-preview__handle pixvisualize-elementor-preview__handle--' . ( $is_vertical ? 'vertical' : 'horizontal' );
$has_image        = $before_url || $after_url;

/* Handle preview (mirrors the Gutenberg Handle component + metabox JS):
   single synchronized control (separator_color) for bar + disc, independent
   arrows color (default #666666), grouped drop-shadow on the parent. */
$sep_rgb         = sscanf( ltrim( $separator_color, '#' ), '%2x%2x%2x' );
$is_transparent  = in_array( $handle, array( 'transparent', 'arrows' ), true );
/* Disc background: legacy explicit color wins, empty follows the separator. */
$disc_bg         = $handle_bg ? $handle_bg : ( $is_transparent ? 'transparent' : $separator_color );
$icon_bg         = $disc_bg;
$icon_color      = $handle_arrow_color ?: '#666666';
/* Inner spacing: on the icon box for custom SVG, on the glyph box else. */
$is_custom_icon  = 'custom' === $handle && $handle_svg;
$icon_padding    = $is_custom_icon ? $handle_pad : 0;
$glyph_padding   = $is_custom_icon ? 0 : $handle_pad;
$handle_inner    = max( 12, (int) round( $handle_size * 0.65 ) );
$handle_arrows   = $handle_size + 10;

if ( 'custom' === $handle && $handle_svg ) {
	$icon_inner = $handle_svg;
} elseif ( 'arrows' === $handle ) {
	$icon_inner = '<span class="pixvisualize-elementor-preview__arrows" style="width: ' . $handle_arrows . 'px; height: ' . $handle_arrows . 'px; padding: ' . $glyph_padding . 'px; box-sizing: border-box;"><svg viewBox="0 0 24 24" aria-hidden="true">' . ( $is_vertical ? '<path d="M7 9l5-5 5 5"/><path d="M7 15l5 5 5-5"/><path d="M12 6v12"/>' : '<path d="M9 7l-5 5 5 5"/><path d="M15 7l5 5-5 5"/><path d="M6 12h12"/>' ) . '</svg></span>';
} elseif ( 'line' !== $handle ) {
	$icon_inner = '<span class="pixvisualize-elementor-preview__arrows" style="width: ' . $handle_inner . 'px; height: ' . $handle_inner . 'px; padding: ' . $glyph_padding . 'px; box-sizing: border-box;"><svg viewBox="0 0 24 24" aria-hidden="true">' . ( $is_vertical ? '<path d="M6 9 L12 4 L18 9"/><path d="M6 15 L12 20 L18 15"/>' : '<path d="M9 6l-5 6 5 6"/><path d="M15 6l5 6-5 6"/>' ) . '</svg></span>';
} else {
	$icon_inner = '';
}

$line_style  = $is_vertical
	? 'height: ' . $separator_thickness . 'px; width: 100%;'
	: 'width: ' . $separator_thickness . 'px; height: 100%;';
$line_style .= ' background-color: ' . $separator_color . '; box-shadow: none;';
$handle_filter = ( '1' === $separator_shadow ) ? 'filter: drop-shadow(0 0 6px rgba(0, 0, 0, 0.6));' : '';

$before_pos_style = 'top: 12px; left: 12px;';
if ( 'top-right' === $label_pos_before )    { $before_pos_style = 'top: 12px; right: 12px;'; }
if ( 'bottom-left' === $label_pos_before )  { $before_pos_style = 'bottom: 12px; left: 12px;'; }
if ( 'bottom-right' === $label_pos_before ) { $before_pos_style = 'bottom: 12px; right: 12px;'; }

$after_pos_style = 'top: 12px; right: 12px;';
if ( 'top-left' === $label_pos_after )     { $after_pos_style = 'top: 12px; left: 12px;'; }
if ( 'bottom-left' === $label_pos_after )  { $after_pos_style = 'bottom: 12px; left: 12px;'; }
if ( 'bottom-right' === $label_pos_after ) { $after_pos_style = 'bottom: 12px; right: 12px;'; }

$label_style = 'background-color: ' . $label_bg . '; color: ' . $label_color
	. '; font-size: ' . $label_font_size . 'px; font-weight: ' . $label_font_weight
	. '; padding: ' . $label_padding_y . 'px ' . $label_padding_x . 'px; border-radius: ' . $label_radius . 'px;';

/* Container styling (mirrors the block CompareSlider inline styles). */
$shadow_alpha = max( 0, min( 100, $container_shadow_opacity ) ) / 100;
$shadow_rgb   = '#000000';
if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $container_shadow_color, $m ) ) {
	$hex = $m[1];
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$shadow_rgb = 'rgba(' . hexdec( substr( $hex, 0, 2 ) ) . ', ' . hexdec( substr( $hex, 2, 2 ) ) . ', ' . hexdec( substr( $hex, 4, 2 ) ) . ', ' . $shadow_alpha . ')';
} else {
	$shadow_rgb = 'rgba(0, 0, 0, ' . $shadow_alpha . ')';
}
$shadow_style = '';
if ( '1' === $container_shadow ) {
	$shadow_style = ' box-shadow: ' . $container_shadow_horizontal . 'px ' . $container_shadow_vertical . 'px ' . $container_shadow_blur . 'px ' . $container_shadow_spread . 'px ' . $shadow_rgb . ';';
}
$border_value = '';
$eff_border_width = $container_border_width > 0 ? $container_border_width : ( '1' === $container_border ? 2 : 0 );
$eff_border_color = $container_border_width > 0 ? $container_border_color : '#E5E7EB';
$eff_border_style = $container_border_width > 0 ? $container_border_style : 'solid';
if ( $eff_border_width > 0 ) {
	$border_value = $eff_border_width . 'px ' . $eff_border_style . ' ' . $eff_border_color;
}
$eff_radius_tl = $container_border_radius_tl > 0 ? $container_border_radius_tl : ( '1' === $container_rounded ? 16 : 0 );
$eff_radius_tr = $container_border_radius_tr > 0 ? $container_border_radius_tr : ( '1' === $container_rounded ? 16 : 0 );
$eff_radius_br = $container_border_radius_br > 0 ? $container_border_radius_br : ( '1' === $container_rounded ? 16 : 0 );
$eff_radius_bl = $container_border_radius_bl > 0 ? $container_border_radius_bl : ( '1' === $container_rounded ? 16 : 0 );

/* Card look (mirrors the block CompareSlider inline styles): with a caption
   the container border/shadow wrap the whole card, without it they stay on
   the slider alone. */
$card_shadow = $caption_visible && '1' === $container_shadow;
/* Card look: with a caption the container bottom corners move to the
   caption (the slider bottom stays square). */
$caption_bottom_br = $eff_radius_br;
$caption_bottom_bl = $eff_radius_bl;

$card_style = 'width: ' . $container_width . '; margin: ' . $container_margin . '; max-width: 100%; box-sizing: border-box;';
if ( $card_shadow ) {
	$card_style .= $shadow_style . ' border-radius: ' . $eff_radius_tl . 'px ' . $eff_radius_tr . 'px ' . $caption_bottom_br . 'px ' . $caption_bottom_bl . 'px;';
}

if ( $caption_visible ) {
	$box_border_style = $eff_border_width > 0
		? ' border-top: ' . $border_value . '; border-left: ' . $border_value . '; border-right: ' . $border_value . ';'
		: '';
	$box_radius_style = ' border-radius: ' . $eff_radius_tl . 'px ' . $eff_radius_tr . 'px 0 0;';
} else {
	$box_border_style = $border_value ? ' border: ' . $border_value . ';' : '';
	$box_radius_style = ' border-radius: ' . $eff_radius_tl . 'px ' . $eff_radius_tr . 'px ' . $eff_radius_br . 'px ' . $eff_radius_bl . 'px;';
}
$box_shadow_style = $card_shadow ? '' : $shadow_style;
$box_width_style  = $caption_visible ? 'width: 100%; margin: 0;' : 'width: ' . $container_width . '; margin: ' . $container_margin . ';';
$box_height_style = $has_image ? ' height: ' . max( 100, min( 2000, $height ) ) . 'px;' : '';

$caption_box_style = 'width: 100%; max-width: 100%; margin: 0; box-sizing: border-box;'
	. ' background-color: ' . $caption_bg . '; text-align: ' . $caption_alignment . '; padding: ' . $caption_padding_y . 'px ' . $caption_padding_x . 'px;'
	. ' border-radius: 0 0 ' . $caption_bottom_br . 'px ' . $caption_bottom_bl . 'px;';
if ( $eff_border_width > 0 ) {
	$caption_box_style .= ' border-left: ' . $border_value . '; border-right: ' . $border_value . '; border-bottom: ' . $border_value . ';';
} else {
	$caption_box_style .= ' border-top: 1px solid #e5e7eb;';
}
$caption_title_style = 'color: ' . $caption_title_color . '; font-size: ' . $caption_title_size . 'px; font-weight: 700; line-height: 1.25; margin: ' . ( '' !== trim( (string) $caption_text ) ? '0 0 4px' : '0' ) . ';';
$caption_text_style  = 'color: ' . $caption_text_color . '; font-size: ' . $caption_text_size . 'px; line-height: 1.5; margin: 0;';

$ratio_options = array(
	'21/9' => '21 : 9',
	'2/1'  => '2 : 1',
	'16/9' => '16 : 9',
	'3/2'  => '3 : 2',
	'4/3'  => '4 : 3',
	'1/1'  => '1 : 1',
	'3/4'  => '3 : 4',
	'2/3'  => '2 : 3',
	'9/16' => '9 : 16',
	'1/2'  => '1 : 2',
	'9/21' => '9 : 21',
);

$label_positions = array(
	'top-left'     => __( 'Top Left', 'pixvisualize' ),
	'top-right'    => __( 'Top Right', 'pixvisualize' ),
	'bottom-left'  => __( 'Bottom Left', 'pixvisualize' ),
	'bottom-right' => __( 'Bottom Right', 'pixvisualize' ),
);

wp_nonce_field( 'pixvisualize_product_save', 'pixvisualize_product_nonce' );
?>
<div id="pixvisualizeduct-data" class="panel woocommerce_options_panel">

	<div class="options_group">
		<div class="pixvisualize-woo-live-preview-wrap">
		<div class="pixvisualize-woo-live-card" style="<?php echo esc_attr( $card_style ); ?>">
		<div
				class="pixvisualize-woo-live-preview pixvisualize-elementor-preview<?php echo $has_image ? '' : ' pixvisualize-elementor-preview--empty'; ?>"
				data-orientation="<?php echo esc_attr( $orientation ); ?>"
				data-aspect-ratio="<?php echo esc_attr( $ratio ); ?>"
				data-height="<?php echo esc_attr( $height ); ?>"
				data-hover-mode="<?php echo '1' === $hover_mode ? '1' : '0'; ?>"
				data-auto-scan="<?php echo '1' === $auto_scan ? '1' : '0'; ?>"
				data-auto-scan-duration="<?php echo esc_attr( $auto_scan_duration ); ?>"
				data-magnifier="<?php echo '1' === $magnifier ? '1' : '0'; ?>"
				data-magnifier-zoom="<?php echo esc_attr( $magnifier_zoom ); ?>"
				style="<?php echo esc_attr( $box_width_style ); ?> aspect-ratio: <?php echo esc_attr( $ratio ); ?>;<?php echo esc_attr( $box_height_style ); ?> --pc-pos: <?php echo esc_attr( $position ); ?>%;<?php echo esc_attr( $box_shadow_style ); ?><?php echo esc_attr( $box_border_style ); ?><?php echo esc_attr( $box_radius_style ); ?>"
			>
				<?php /* Always rendered (even empty): the admin script fills them when images are picked. */ ?>
				<img class="pixvisualize-elementor-preview__image pixvisualize-elementor-preview__after" src="<?php echo esc_url( $after_url ); ?>" alt=""<?php echo $after_url ? '' : ' style="display: none;"'; ?>>
				<img class="pixvisualize-elementor-preview__image pixvisualize-elementor-preview__before" src="<?php echo esc_url( $before_url ); ?>" alt=""<?php echo $before_url ? '' : ' style="display: none;"'; ?>>
				<div class="<?php echo esc_attr( $handle_class ); ?>"<?php echo $has_image ? ( $handle_filter ? ' style="' . esc_attr( $handle_filter ) . '"' : '' ) : ' style="display: none;"'; ?>>
					<div class="pixvisualize-elementor-preview__handle-line" style="<?php echo esc_attr( $line_style ); ?>"></div>
<?php
// SVG allowlist for the custom handle icon preview (admin-only field,
// mirrors the block editor sanitization).
$pixvisualize_allowed_svg = array(
	'span'    => array( 'class' => array(), 'style' => array() ),
	'svg'     => array(
		'viewBox' => array(), 'xmlns' => array(), 'preserveAspectRatio' => array(),
		'aria-hidden' => array(), 'focusable' => array(), 'width' => array(), 'height' => array(),
	),
	'g'       => array( 'transform' => array(), 'fill' => array(), 'stroke' => array() ),
	'path'    => array( 'd' => array(), 'fill' => array(), 'stroke' => array(), 'stroke-width' => array(), 'stroke-linecap' => array(), 'stroke-linejoin' => array() ),
	'circle'  => array( 'cx' => array(), 'cy' => array(), 'r' => array(), 'fill' => array() ),
	'rect'    => array( 'x' => array(), 'y' => array(), 'width' => array(), 'height' => array(), 'fill' => array() ),
	'polygon' => array( 'points' => array(), 'fill' => array() ),
);
?>
					<div class="pixvisualize-elementor-preview__handle-icon"<?php echo 'line' === $handle ? ' style="display: none;"' : ' style="width: ' . esc_attr( $handle_size ) . 'px; height: ' . esc_attr( $handle_size ) . 'px; background-color: ' . esc_attr( $icon_bg ) . '; color: ' . esc_attr( $icon_color ) . '; --svg-fill: ' . esc_attr( $icon_color ) . '; border-radius: 50%; overflow: hidden; box-sizing: border-box; padding: ' . esc_attr( $icon_padding ) . 'px; box-shadow: none;"'; ?>>
						<?php echo wp_kses( $icon_inner, $pixvisualize_allowed_svg ); ?>
					</div>
				</div>
				<span class="pixvisualize-elementor-preview__label pixvisualize-elementor-preview__label-before" style="<?php echo esc_attr( $label_style ); ?>; <?php echo esc_attr( $before_pos_style ); ?>;<?php echo ( $has_image && '1' === $show_labels ) ? '' : ' display: none;'; ?>"><?php echo esc_html( $before_label ); ?></span>
				<span class="pixvisualize-elementor-preview__label pixvisualize-elementor-preview__label-after" style="<?php echo esc_attr( $label_style ); ?>; <?php echo esc_attr( $after_pos_style ); ?>;<?php echo ( $has_image && '1' === $show_labels ) ? '' : ' display: none;'; ?>"><?php echo esc_html( $after_label ); ?></span>
				<span class="pixvisualize-woo-live-empty"<?php echo $has_image ? ' style="display: none;"' : ''; ?>><?php esc_html_e( 'Select your Before and After images to display the comparison slider.', 'pixvisualize' ); ?></span>
			</div>
			<div class="pixvisualize-woo-live-caption" style="<?php echo esc_attr( $caption_box_style ); ?><?php echo $caption_visible ? '' : ' display: none;'; ?>">
				<?php if ( '' !== trim( (string) $caption_title ) ) : ?>
					<div class="pixvisualize-woo-live-caption-title" style="<?php echo esc_attr( $caption_title_style ); ?>"><?php echo esc_html( $caption_title ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $caption_text ) ) : ?>
					<div class="pixvisualize-woo-live-caption-text" style="<?php echo esc_attr( $caption_text_style ); ?>"><?php echo esc_html( $caption_text ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		</div>
	</div>

	<details class="pixvisualize-woo-accordion options_group" open>
		<summary class="pixvisualize-woo-group-title"><?php esc_html_e( 'Images & Content', 'pixvisualize' ); ?></summary>

		<div class="pixvisualize-woo-image-row">
			<div class="pixvisualize-woo-image-col">
				<label class="pixvisualize-woo-image-label"><?php esc_html_e( 'Before Image', 'pixvisualize' ); ?></label>
				<?php $target = 'before'; $image_id = $before_id; include __DIR__ . '/woocommerce-picker.php'; ?>
			</div>
			<div class="pixvisualize-woo-image-col">
				<label class="pixvisualize-woo-image-label"><?php esc_html_e( 'After Image', 'pixvisualize' ); ?></label>
				<?php $target = 'after'; $image_id = $after_id; include __DIR__ . '/woocommerce-picker.php'; ?>
			</div>
		</div>

		<p class="form-field pixvisualize-woo-label-field">
			<label for="pixvisualize_woo_before_label"><?php esc_html_e( 'Before Label', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_before_label" name="pixvisualize_woo_before_label" class="short" value="<?php echo esc_attr( $before_label ); ?>">
		</p>

		<p class="form-field pixvisualize-woo-label-field">
			<label for="pixvisualize_woo_after_label"><?php esc_html_e( 'After Label', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_after_label" name="pixvisualize_woo_after_label" class="short" value="<?php echo esc_attr( $after_label ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_show_labels"><?php esc_html_e( 'Show Labels', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_show_labels" name="pixvisualize_woo_show_labels" value="1" <?php checked( $show_labels, '1' ); ?>>
		</p>

		<p class="form-field pixvisualize-woo-label-field">
			<label for="pixvisualize_woo_label_position_before"><?php esc_html_e( 'Before Label Position', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_label_position_before" name="pixvisualize_woo_label_position_before" class="short">
				<?php foreach ( $label_positions as $value => $display ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $label_pos_before, $value ); ?>><?php echo esc_html( $display ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="form-field pixvisualize-woo-label-field">
			<label for="pixvisualize_woo_label_position_after"><?php esc_html_e( 'After Label Position', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_label_position_after" name="pixvisualize_woo_label_position_after" class="short">
				<?php foreach ( $label_positions as $value => $display ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $label_pos_after, $value ); ?>><?php echo esc_html( $display ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	</details>

	<details class="pixvisualize-woo-accordion options_group">
		<summary class="pixvisualize-woo-group-title"><?php esc_html_e( 'Caption', 'pixvisualize' ); ?></summary>

		<p class="form-field">
			<label for="pixvisualize_woo_show_caption"><?php esc_html_e( 'Show Caption', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_show_caption" name="pixvisualize_woo_show_caption" value="1" <?php checked( $show_caption, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Uncheck to hide the title / text block below the slider.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_title"><?php esc_html_e( 'Caption Title', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_caption_title" name="pixvisualize_woo_caption_title" class="short" value="<?php echo esc_attr( $caption_title ); ?>" placeholder="<?php esc_attr_e( 'One object, two realities', 'pixvisualize' ); ?>">
			<span class="description"><?php esc_html_e( 'Bold title displayed below the slider.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_text"><?php esc_html_e( 'Caption Text', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_caption_text" name="pixvisualize_woo_caption_text" class="short" value="<?php echo esc_attr( $caption_text ); ?>" placeholder="<?php esc_attr_e( 'See the before and after difference.', 'pixvisualize' ); ?>">
			<span class="description"><?php esc_html_e( 'Small text displayed under the caption title.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_bg"><?php esc_html_e( 'Caption Background', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_caption_bg" name="pixvisualize_woo_caption_bg" class="short pixvisualize-woo-colorpicker" data-default-color="#ffffff" value="<?php echo esc_attr( $caption_bg ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_title_color"><?php esc_html_e( 'Caption Title Color', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_caption_title_color" name="pixvisualize_woo_caption_title_color" class="short pixvisualize-woo-colorpicker" data-default-color="#1e293b" value="<?php echo esc_attr( $caption_title_color ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_text_color"><?php esc_html_e( 'Caption Text Color', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_caption_text_color" name="pixvisualize_woo_caption_text_color" class="short pixvisualize-woo-colorpicker" data-default-color="#64748b" value="<?php echo esc_attr( $caption_text_color ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_alignment"><?php esc_html_e( 'Caption Alignment', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_caption_alignment" name="pixvisualize_woo_caption_alignment" class="short">
				<option value="left" <?php selected( $caption_alignment, 'left' ); ?>><?php esc_html_e( 'Left', 'pixvisualize' ); ?></option>
				<option value="center" <?php selected( $caption_alignment, 'center' ); ?>><?php esc_html_e( 'Center', 'pixvisualize' ); ?></option>
				<option value="right" <?php selected( $caption_alignment, 'right' ); ?>><?php esc_html_e( 'Right', 'pixvisualize' ); ?></option>
			</select>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_title_size"><?php esc_html_e( 'Caption Title Size (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_caption_title_size" name="pixvisualize_woo_caption_title_size" class="short" min="12" max="48" step="1" value="<?php echo esc_attr( $caption_title_size ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_text_size"><?php esc_html_e( 'Caption Text Size (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_caption_text_size" name="pixvisualize_woo_caption_text_size" class="short" min="10" max="32" step="1" value="<?php echo esc_attr( $caption_text_size ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_padding_x"><?php esc_html_e( 'Caption Padding X (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_caption_padding_x" name="pixvisualize_woo_caption_padding_x" class="short" min="0" max="60" step="1" value="<?php echo esc_attr( $caption_padding_x ); ?>">
			<span class="description"><?php esc_html_e( 'Left/right inner spacing of the caption.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_caption_padding_y"><?php esc_html_e( 'Caption Padding Y (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_caption_padding_y" name="pixvisualize_woo_caption_padding_y" class="short" min="0" max="60" step="1" value="<?php echo esc_attr( $caption_padding_y ); ?>">
			<span class="description"><?php esc_html_e( 'Top/bottom inner spacing of the caption: controls its height.', 'pixvisualize' ); ?></span>
		</p>

	</details>

	<details class="pixvisualize-woo-accordion options_group">
		<summary class="pixvisualize-woo-group-title"><?php esc_html_e( 'Placement', 'pixvisualize' ); ?></summary>

		<p class="form-field">
			<label for="pixvisualize_woo_placement"><?php esc_html_e( 'Product page placement', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_placement" name="pixvisualize_woo_placement" class="short">
				<?php if ( $show_placement_group( $placement_group_main ) ) : ?>
				<optgroup label="<?php esc_attr_e( 'Main image area', 'pixvisualize' ); ?>">
					<?php if ( $show_placement( 'above_gallery' ) ) : ?><option value="above_gallery" <?php selected( $placement, 'above_gallery' ); ?>><?php esc_html_e( 'Above the gallery', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'above_main_image' ) ) : ?><option value="above_main_image" <?php selected( $placement, 'above_main_image' ); ?>><?php esc_html_e( 'Above the main image', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'under_thumbnails' ) ) : ?><option value="under_thumbnails" <?php selected( $placement, 'under_thumbnails' ); ?>><?php esc_html_e( 'Under the gallery thumbnails', 'pixvisualize' ); ?></option><?php endif; ?>
				</optgroup>
				<?php endif; ?>
				<?php if ( $show_placement_group( $placement_group_summary ) ) : ?>
				<optgroup label="<?php esc_attr_e( 'Summary & Buy Area (Right Column)', 'pixvisualize' ); ?>">
					<?php if ( $show_placement( 'summary_before_title' ) ) : ?><option value="summary_before_title" <?php selected( $placement, 'summary_before_title' ); ?>><?php esc_html_e( 'Before the product title', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'summary_after_title' ) ) : ?><option value="summary_after_title" <?php selected( $placement, 'summary_after_title' ); ?>><?php esc_html_e( 'Between the title and the rating', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'summary_after_price' ) ) : ?><option value="summary_after_price" <?php selected( $placement, 'summary_after_price' ); ?>><?php esc_html_e( 'Below the price', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'summary_after_excerpt' ) ) : ?><option value="summary_after_excerpt" <?php selected( $placement, 'summary_after_excerpt' ); ?>><?php esc_html_e( 'Below the short description', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'summary_after_cart' ) ) : ?><option value="summary_after_cart" <?php selected( $placement, 'summary_after_cart' ); ?>><?php esc_html_e( 'Below the add to cart button', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'summary_end' ) ) : ?><option value="summary_end" <?php selected( $placement, 'summary_end' ); ?>><?php esc_html_e( 'End of the summary', 'pixvisualize' ); ?></option><?php endif; ?>
				</optgroup>
				<?php endif; ?>
				<?php if ( $show_placement_group( $placement_group_after ) ) : ?>
				<optgroup label="<?php esc_attr_e( 'After the summary', 'pixvisualize' ); ?>">
					<?php if ( $show_placement( 'after_summary_before_tabs' ) ) : ?><option value="after_summary_before_tabs" <?php selected( $placement, 'after_summary_before_tabs' ); ?>><?php esc_html_e( 'Before the product tabs', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'after_summary_after_tabs' ) ) : ?><option value="after_summary_after_tabs" <?php selected( $placement, 'after_summary_after_tabs' ); ?>><?php esc_html_e( 'Between the tabs and the related products', 'pixvisualize' ); ?></option><?php endif; ?>
					<?php if ( $show_placement( 'after_summary_after_related' ) ) : ?><option value="after_summary_after_related" <?php selected( $placement, 'after_summary_after_related' ); ?>><?php esc_html_e( 'After the related products', 'pixvisualize' ); ?></option><?php endif; ?>
				</optgroup>
				<?php endif; ?>
				<?php if ( $show_placement( 'after_single_product' ) ) : ?>
				<optgroup label="<?php esc_attr_e( 'End of page', 'pixvisualize' ); ?>">
					<option value="after_single_product" <?php selected( $placement, 'after_single_product' ); ?>><?php esc_html_e( 'End of the product page', 'pixvisualize' ); ?></option>
				</optgroup>
				<?php endif; ?>
				<?php if ( $show_placement( 'product_tab' ) ) : ?>
				<optgroup label="<?php esc_attr_e( 'Product tab', 'pixvisualize' ); ?>">
					<option value="product_tab" <?php selected( $placement, 'product_tab' ); ?>><?php esc_html_e( 'Add a dedicated custom tab for the comparison', 'pixvisualize' ); ?></option>
				</optgroup>
				<?php endif; ?>
			</select>
			<span class="description"><?php esc_html_e( 'Where the comparison appears on the single product page.', 'pixvisualize' ); ?></span>
			<?php if ( $placement_scan_warning ) : ?>
				<span class="description pixvisualize-woo-scan-warning"><?php esc_html_e( 'Classic WooCommerce hooks were not detected in the active theme (block theme?): placements may not display on the product page.', 'pixvisualize' ); ?></span>
			<?php elseif ( $hidden_placements ) : ?>
				<span class="description"><?php
					/* translators: %d: number of hidden placements. */
					echo esc_html( sprintf( _n( '%d placement hidden — not detected in the active theme.', '%d placements hidden — not detected in the active theme.', count( $hidden_placements ), 'pixvisualize' ) ) );
				?></span>
			<?php endif; ?>
			<?php if ( $saved_placement_missing ) : ?>
				<span class="description pixvisualize-woo-scan-warning"><?php esc_html_e( 'The saved placement was not detected in the active theme and may not display.', 'pixvisualize' ); ?></span>
			<?php endif; ?>
		</p>

		<div class="pixvisualize-woo-tab-fields"<?php echo 'product_tab' === $placement ? '' : ' style="display: none;"'; ?>>
			<h4 class="pixvisualize-woo-group-title pixvisualize-woo-subtitle"><?php esc_html_e( 'Comparison tab', 'pixvisualize' ); ?></h4>

			<p class="form-field">
				<label for="pixvisualize_woo_tab_title"><?php esc_html_e( 'Tab Title', 'pixvisualize' ); ?></label>
				<input type="text" id="pixvisualize_woo_tab_title" name="pixvisualize_woo_tab_title" class="short" value="<?php echo esc_attr( $tab_title ); ?>">
				<span class="description"><?php esc_html_e( 'Title of the comparison tab on the single product page.', 'pixvisualize' ); ?></span>
			</p>
		</div>

		<h4 class="pixvisualize-woo-group-title pixvisualize-woo-subtitle"><?php esc_html_e( 'Content', 'pixvisualize' ); ?></h4>

		<div class="form-field pixvisualize-woo-desc-editor">
			<label for="pixvisualize_woo_desc"><?php esc_html_e( 'Description', 'pixvisualize' ); ?></label>
			<?php
			wp_editor(
				$desc,
				'pixvisualize_woo_desc',
				array(
					'textarea_name' => 'pixvisualize_woo_desc',
					'textarea_rows' => 4,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => true,
				)
			);
			?>
			<span class="description"><?php esc_html_e( 'Accompanying text displayed with the comparator, whatever the chosen placement.', 'pixvisualize' ); ?></span>
		</div>

		<p class="form-field">
			<label for="pixvisualize_woo_desc_layout"><?php esc_html_e( 'Content layout', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_desc_layout" name="pixvisualize_woo_desc_layout" class="short">
				<option value="top" <?php selected( $desc_layout, 'top' ); ?>><?php esc_html_e( 'Description above the comparator', 'pixvisualize' ); ?></option>
				<option value="bottom" <?php selected( $desc_layout, 'bottom' ); ?>><?php esc_html_e( 'Description below the comparator', 'pixvisualize' ); ?></option>
				<option value="left" <?php selected( $desc_layout, 'left' ); ?>><?php esc_html_e( '2 columns: description on the left, comparator on the right', 'pixvisualize' ); ?></option>
				<option value="right" <?php selected( $desc_layout, 'right' ); ?>><?php esc_html_e( '2 columns: comparator on the left, description on the right', 'pixvisualize' ); ?></option>
			</select>
			<span class="description"><?php esc_html_e( 'Position of the description relative to the comparator.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_desc_vertical"><?php esc_html_e( 'Description vertical position', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_desc_vertical" name="pixvisualize_woo_desc_vertical" class="short">
				<option value="top" <?php selected( $desc_valign, 'top' ); ?>><?php esc_html_e( 'Text at the top', 'pixvisualize' ); ?></option>
				<option value="middle" <?php selected( $desc_valign, 'middle' ); ?>><?php esc_html_e( 'Text in the middle', 'pixvisualize' ); ?></option>
				<option value="bottom" <?php selected( $desc_valign, 'bottom' ); ?>><?php esc_html_e( 'Text at the bottom', 'pixvisualize' ); ?></option>
			</select>
			<span class="description"><?php esc_html_e( 'Vertical position of the text inside its container (used in 2-column layouts).', 'pixvisualize' ); ?></span>
		</p>
	</details>

	<details class="pixvisualize-woo-accordion options_group">
		<summary class="pixvisualize-woo-group-title"><?php esc_html_e( 'Layout & Orientation', 'pixvisualize' ); ?></summary>

		<p class="form-field">
			<label for="pixvisualize_woo_orientation"><?php esc_html_e( 'Orientation', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_orientation" name="pixvisualize_woo_orientation" class="short">
				<option value="horizontal" <?php selected( $orientation, 'horizontal' ); ?>><?php esc_html_e( 'Horizontal', 'pixvisualize' ); ?></option>
				<option value="vertical" <?php selected( $orientation, 'vertical' ); ?>><?php esc_html_e( 'Vertical', 'pixvisualize' ); ?></option>
			</select>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_ratio"><?php esc_html_e( 'Aspect Ratio', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_ratio" name="pixvisualize_woo_ratio" class="short">
				<?php foreach ( $ratio_options as $value => $display ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $ratio, $value ); ?>><?php echo esc_html( $display ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="description"><?php esc_html_e( 'Width / height proportion, always respected (contain): 21/9 fills the width, 9/21 fills the height.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_height"><?php esc_html_e( 'Slider Height (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_height" name="pixvisualize_woo_height" class="short" min="50" max="2000" step="10" value="<?php echo esc_attr( $height ); ?>">
			<span class="description"><?php esc_html_e( 'Desired zone height, capped by a fixed parent height (contain).', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_full_width"><?php esc_html_e( 'Full Width', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_full_width" name="pixvisualize_woo_full_width" value="1" <?php checked( $full_width, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Spans the content width (ratio kept, centered). Useless with a fixed parent height — contain already caps it.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_position"><?php esc_html_e( 'Initial Position (%)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_position" name="pixvisualize_woo_position" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $position ); ?>">
			<span class="description"><?php esc_html_e( 'Where the slider starts (0 = fully Before, 100 = fully After).', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_alignment"><?php esc_html_e( 'Alignment', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_alignment" name="pixvisualize_woo_alignment" class="short">
				<option value="left" <?php selected( $alignment, 'left' ); ?>><?php esc_html_e( 'Left', 'pixvisualize' ); ?></option>
				<option value="center" <?php selected( $alignment, 'center' ); ?>><?php esc_html_e( 'Center', 'pixvisualize' ); ?></option>
				<option value="right" <?php selected( $alignment, 'right' ); ?>><?php esc_html_e( 'Right', 'pixvisualize' ); ?></option>
			</select>
		</p>

		<h4 class="pixvisualize-woo-group-title pixvisualize-woo-subtitle"><?php esc_html_e( 'Responsive', 'pixvisualize' ); ?></h4>

		<p class="form-field">
			<label for="pixvisualize_woo_tablet_position"><?php esc_html_e( 'Slider Position on Tablet (%)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_tablet_position" name="pixvisualize_woo_tablet_position" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $tablet_position ); ?>">
			<span class="description"><?php esc_html_e( 'Divider position on tablets: 0 = fully "Before", 100 = fully "After".', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_mobile_position"><?php esc_html_e( 'Slider Position on Mobile (%)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_mobile_position" name="pixvisualize_woo_mobile_position" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $mobile_position ); ?>">
			<span class="description"><?php esc_html_e( 'Divider position on phones: 0 = fully "Before", 100 = fully "After".', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_tablet_ratio"><?php esc_html_e( 'Tablet Ratio', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_tablet_ratio" name="pixvisualize_woo_tablet_ratio" class="short">
				<option value="" <?php selected( $tablet_ratio, '' ); ?>><?php esc_html_e( 'Inherit (desktop)', 'pixvisualize' ); ?></option>
				<?php foreach ( $ratio_options as $value => $display ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $tablet_ratio, $value ); ?>><?php echo esc_html( $display ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_mobile_ratio"><?php esc_html_e( 'Mobile Ratio', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_mobile_ratio" name="pixvisualize_woo_mobile_ratio" class="short">
				<option value="" <?php selected( $mobile_ratio, '' ); ?>><?php esc_html_e( 'Inherit (desktop)', 'pixvisualize' ); ?></option>
				<?php foreach ( $ratio_options as $value => $display ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $mobile_ratio, $value ); ?>><?php echo esc_html( $display ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	</details>

	<details class="pixvisualize-woo-accordion options_group">
		<summary class="pixvisualize-woo-group-title"><?php esc_html_e( 'Appearance', 'pixvisualize' ); ?></summary>

		<p class="form-field">
			<label for="pixvisualize_woo_handle"><?php esc_html_e( 'Handle Style', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_handle" name="pixvisualize_woo_handle" class="short">
				<option value="circle" <?php selected( $handle, 'circle' ); ?>><?php esc_html_e( 'Circle', 'pixvisualize' ); ?></option>
				<option value="transparent" <?php selected( $handle, 'transparent' ); ?>><?php esc_html_e( 'Transparent', 'pixvisualize' ); ?></option>
				<option value="line" <?php selected( $handle, 'line' ); ?>><?php esc_html_e( 'Line', 'pixvisualize' ); ?></option>
				<option value="arrows" <?php selected( $handle, 'arrows' ); ?>><?php esc_html_e( 'Arrows', 'pixvisualize' ); ?></option>
				<option value="custom" <?php selected( $handle, 'custom' ); ?>><?php esc_html_e( 'Custom SVG', 'pixvisualize' ); ?></option>
			</select>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_handle_svg"><?php esc_html_e( 'Custom SVG Icon', 'pixvisualize' ); ?></label>
			<textarea id="pixvisualize_woo_handle_svg" name="pixvisualize_woo_handle_svg" class="short" rows="3" placeholder="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;currentColor&quot;>…</svg>"><?php echo esc_textarea( $handle_svg ); ?></textarea>
			<span class="description"><?php esc_html_e( 'Paste <svg viewBox="…">…</svg> only. Only used when Handle Style is Custom SVG. Use fill="currentColor" so the icon inherits Handle Arrows Color.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_handle_size"><?php esc_html_e( 'Handle Size (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_handle_size" name="pixvisualize_woo_handle_size" class="short" min="16" max="64" step="1" value="<?php echo esc_attr( $handle_size ); ?>">
			<span class="description"><?php esc_html_e( 'Diameter of the handle circle: independent size control.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_handle_arrow_color"><?php esc_html_e( 'Handle Arrows Color', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_handle_arrow_color" name="pixvisualize_woo_handle_arrow_color" class="short pixvisualize-woo-colorpicker" data-default-color="#666666" value="<?php echo esc_attr( $handle_arrow_color ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
			<span class="description"><?php esc_html_e( 'Color of the arrows inside the handle (independent control).', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_handle_padding"><?php esc_html_e( 'Handle Padding (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_handle_padding" name="pixvisualize_woo_handle_padding" class="short" min="0" max="20" step="1" value="<?php echo esc_attr( $handle_pad ); ?>">
			<span class="description"><?php esc_html_e( 'Inner spacing between the icon and the edge of the handle background, in px.', 'pixvisualize' ); ?></span>
		</p>

		<h4 class="pixvisualize-woo-group-title pixvisualize-woo-subtitle"><?php esc_html_e( 'Colors', 'pixvisualize' ); ?></h4>

		<p class="form-field">
			<label for="pixvisualize_woo_separator_thickness"><?php esc_html_e( 'Separator Thickness (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_separator_thickness" name="pixvisualize_woo_separator_thickness" class="short" min="1" max="10" step="1" value="<?php echo esc_attr( $separator_thickness ); ?>">
			<span class="description"><?php esc_html_e( 'Thickness of the vertical bar: independent control.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_separator_color"><?php esc_html_e( 'Cursor & Handle Color', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_separator_color" name="pixvisualize_woo_separator_color" class="short pixvisualize-woo-colorpicker" data-default-color="#ffffff" value="<?php echo esc_attr( $separator_color ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
			<span class="description"><?php esc_html_e( 'Single synchronized control: background of the vertical bar and the handle circle.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_separator_shadow"><?php esc_html_e( 'Drop Shadow', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_separator_shadow" name="pixvisualize_woo_separator_shadow" value="1" <?php checked( $separator_shadow, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Grouped shadow on the whole cursor + handle (filter: drop-shadow, no seam at the junction).', 'pixvisualize' ); ?></span>
		</p>

		<h4 class="pixvisualize-woo-group-title pixvisualize-woo-subtitle"><?php esc_html_e( 'Container Style', 'pixvisualize' ); ?></h4>

		<p class="form-field">
			<label for="pixvisualize_woo_container_shadow"><?php esc_html_e( 'Container Shadow', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_container_shadow" name="pixvisualize_woo_container_shadow" value="1" <?php checked( $container_shadow, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Add a drop shadow around the comparison container.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field pixvisualize-woo-container-shadow-field">
			<label for="pixvisualize_woo_container_shadow_color"><?php esc_html_e( 'Shadow Color', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_container_shadow_color" name="pixvisualize_woo_container_shadow_color" class="short pixvisualize-woo-colorpicker" data-default-color="#000000" value="<?php echo esc_attr( $container_shadow_color ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
		</p>

		<p class="form-field pixvisualize-woo-container-shadow-field">
			<label for="pixvisualize_woo_container_shadow_horizontal"><?php esc_html_e( 'Shadow Horizontal Offset (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_shadow_horizontal" name="pixvisualize_woo_container_shadow_horizontal" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_shadow_horizontal ); ?>">
		</p>

		<p class="form-field pixvisualize-woo-container-shadow-field">
			<label for="pixvisualize_woo_container_shadow_vertical"><?php esc_html_e( 'Shadow Vertical Offset (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_shadow_vertical" name="pixvisualize_woo_container_shadow_vertical" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_shadow_vertical ); ?>">
		</p>

		<p class="form-field pixvisualize-woo-container-shadow-field">
			<label for="pixvisualize_woo_container_shadow_blur"><?php esc_html_e( 'Shadow Blur (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_shadow_blur" name="pixvisualize_woo_container_shadow_blur" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_shadow_blur ); ?>">
		</p>

		<p class="form-field pixvisualize-woo-container-shadow-field">
			<label for="pixvisualize_woo_container_shadow_spread"><?php esc_html_e( 'Shadow Spread (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_shadow_spread" name="pixvisualize_woo_container_shadow_spread" class="short" min="-50" max="100" step="1" value="<?php echo esc_attr( $container_shadow_spread ); ?>">
		</p>

		<p class="form-field pixvisualize-woo-container-shadow-field">
			<label for="pixvisualize_woo_container_shadow_opacity"><?php esc_html_e( 'Shadow Opacity (%)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_shadow_opacity" name="pixvisualize_woo_container_shadow_opacity" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_shadow_opacity ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border"><?php esc_html_e( 'Show Border', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_container_border" name="pixvisualize_woo_container_border" value="1" <?php checked( $container_border, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Show a default 2px solid gray border around the comparison container.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border_width"><?php esc_html_e( 'Container Border Width (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_border_width" name="pixvisualize_woo_container_border_width" class="short" min="0" max="20" step="1" value="<?php echo esc_attr( $container_border_width ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border_color"><?php esc_html_e( 'Container Border Color', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_container_border_color" name="pixvisualize_woo_container_border_color" class="short pixvisualize-woo-colorpicker" data-default-color="#000000" value="<?php echo esc_attr( $container_border_color ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border_style"><?php esc_html_e( 'Container Border Style', 'pixvisualize' ); ?></label>
			<select id="pixvisualize_woo_container_border_style" name="pixvisualize_woo_container_border_style" class="short">
				<option value="solid" <?php selected( $container_border_style, 'solid' ); ?>><?php esc_html_e( 'Solid', 'pixvisualize' ); ?></option>
				<option value="dashed" <?php selected( $container_border_style, 'dashed' ); ?>><?php esc_html_e( 'Dashed', 'pixvisualize' ); ?></option>
				<option value="dotted" <?php selected( $container_border_style, 'dotted' ); ?>><?php esc_html_e( 'Dotted', 'pixvisualize' ); ?></option>
				<option value="double" <?php selected( $container_border_style, 'double' ); ?>><?php esc_html_e( 'Double', 'pixvisualize' ); ?></option>
			</select>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_rounded"><?php esc_html_e( 'Rounded Corners', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_container_rounded" name="pixvisualize_woo_container_rounded" value="1" <?php checked( $container_rounded, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Round all corners with the default 16px radius.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border_radius_tl"><?php esc_html_e( 'Border Radius – Top Left (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_border_radius_tl" name="pixvisualize_woo_container_border_radius_tl" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_border_radius_tl ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border_radius_tr"><?php esc_html_e( 'Border Radius – Top Right (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_border_radius_tr" name="pixvisualize_woo_container_border_radius_tr" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_border_radius_tr ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border_radius_br"><?php esc_html_e( 'Border Radius – Bottom Right (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_border_radius_br" name="pixvisualize_woo_container_border_radius_br" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_border_radius_br ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_container_border_radius_bl"><?php esc_html_e( 'Border Radius – Bottom Left (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_container_border_radius_bl" name="pixvisualize_woo_container_border_radius_bl" class="short" min="0" max="100" step="1" value="<?php echo esc_attr( $container_border_radius_bl ); ?>">
		</p>

		<h4 class="pixvisualize-woo-group-title pixvisualize-woo-subtitle"><?php esc_html_e( 'Label Typography & Style', 'pixvisualize' ); ?></h4>

		<p class="form-field">
			<label for="pixvisualize_woo_label_background"><?php esc_html_e( 'Label Background', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_label_background" name="pixvisualize_woo_label_background" class="short pixvisualize-woo-colorpicker" data-default-color="#00000080" value="<?php echo esc_attr( $label_bg ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_label_color"><?php esc_html_e( 'Text Color', 'pixvisualize' ); ?></label>
			<input type="text" id="pixvisualize_woo_label_color" name="pixvisualize_woo_label_color" class="short pixvisualize-woo-colorpicker" data-default-color="#ffffff" value="<?php echo esc_attr( $label_color ); ?>" placeholder="#RRGGBB / #RRGGBBAA">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_label_font_size"><?php esc_html_e( 'Font Size (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_label_font_size" name="pixvisualize_woo_label_font_size" class="short" min="10" max="24" step="1" value="<?php echo esc_attr( $label_font_size ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_label_font_weight"><?php esc_html_e( 'Font Weight (400–900)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_label_font_weight" name="pixvisualize_woo_label_font_weight" class="short" min="400" max="900" step="100" value="<?php echo esc_attr( $label_font_weight ); ?>">
			<span class="description"><?php esc_html_e( 'Thickness of the label text (400 = normal, 700 = bold).', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_label_padding_x"><?php esc_html_e( 'Label Padding – Horizontal (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_label_padding_x" name="pixvisualize_woo_label_padding_x" class="short" min="0" max="30" step="1" value="<?php echo esc_attr( $label_padding_x ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_label_padding_y"><?php esc_html_e( 'Label Padding – Vertical (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_label_padding_y" name="pixvisualize_woo_label_padding_y" class="short" min="0" max="30" step="1" value="<?php echo esc_attr( $label_padding_y ); ?>">
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_label_border_radius"><?php esc_html_e( 'Border Radius (px)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_label_border_radius" name="pixvisualize_woo_label_border_radius" class="short" min="0" max="20" step="1" value="<?php echo esc_attr( $label_radius ); ?>">
		</p>
	</details>

	<details class="pixvisualize-woo-accordion options_group">
		<summary class="pixvisualize-woo-group-title"><?php esc_html_e( 'Interactions', 'pixvisualize' ); ?></summary>

		<p class="form-field">
			<label for="pixvisualize_woo_hover_mode"><?php esc_html_e( 'Move on Hover', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_hover_mode" name="pixvisualize_woo_hover_mode" value="1" <?php checked( $hover_mode, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Follow the cursor position over the image.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_auto_scan"><?php esc_html_e( 'Auto-Slide Animation', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_auto_scan" name="pixvisualize_woo_auto_scan" value="1" <?php checked( $auto_scan, '1' ); ?>>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_auto_scan_duration"><?php esc_html_e( 'Auto-Slide Duration (ms)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_auto_scan_duration" name="pixvisualize_woo_auto_scan_duration" class="short" min="1000" max="10000" step="100" value="<?php echo esc_attr( $auto_scan_duration ); ?>">
			<span class="description"><?php esc_html_e( 'How long the auto-slide takes to sweep from 100% to 0% then back to the initial position.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_magnifier"><?php esc_html_e( 'Magnifier', 'pixvisualize' ); ?></label>
			<input type="checkbox" class="checkbox" id="pixvisualize_woo_magnifier" name="pixvisualize_woo_magnifier" value="1" <?php checked( $magnifier, '1' ); ?>>
			<span class="description"><?php esc_html_e( 'Show a zoomed lens around the cursor while hovering the comparison.', 'pixvisualize' ); ?></span>
		</p>

		<p class="form-field">
			<label for="pixvisualize_woo_magnifier_zoom"><?php esc_html_e( 'Magnifier Zoom (x)', 'pixvisualize' ); ?></label>
			<input type="number" id="pixvisualize_woo_magnifier_zoom" name="pixvisualize_woo_magnifier_zoom" class="short" min="1.5" max="4" step="0.5" value="<?php echo esc_attr( $magnifier_zoom ); ?>">
			<span class="description"><?php esc_html_e( 'Zoom factor of the magnifier lens.', 'pixvisualize' ); ?></span>
		</p>
	</details>

	<?php /* Floating shortcuts (fixed bottom-right): jump to the live preview
	         and back to the section being configured. Hidden with the panel
	         when another Product Data tab is active. */ ?>
	<div class="pixvisualize-woo-floatbar inar-pixvisualize-woo-floatbar">
		<button type="button" class="button button-primary pixvisualize-woo-float-preview inar-pixvisualize-woo-float-preview">
			<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
			<span class="pixvisualize-woo-float-label inar-pixvisualize-woo-float-label"><?php esc_html_e( 'Preview', 'pixvisualize' ); ?></span>
		</button>
		<button type="button" class="button pixvisualize-woo-float-back inar-pixvisualize-woo-float-back" style="display: none;">
			<span class="dashicons dashicons-arrow-up-alt" aria-hidden="true"></span>
			<span class="pixvisualize-woo-float-label inar-pixvisualize-woo-float-label"><?php esc_html_e( 'Back to settings', 'pixvisualize' ); ?></span>
		</button>
	</div>
	<style>
		.pixvisualize-woo-floatbar,
		.inar-pixvisualize-woo-floatbar {
			position: fixed;
			right: 20px;
			bottom: 20px;
			z-index: 1000;
			display: flex;
			gap: 8px;
		}
		.pixvisualize-woo-floatbar .button,
		.inar-pixvisualize-woo-floatbar .button {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 6px;
			min-height: 36px;
			line-height: 1.4;
			vertical-align: middle;
			opacity: 1;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
		}
		.pixvisualize-woo-floatbar .button-primary,
		.inar-pixvisualize-woo-floatbar .button-primary {
			background: #2271b1;
			border-color: #2271b1;
			color: #fff;
		}
		.pixvisualize-woo-floatbar .pixvisualize-woo-float-back,
		.inar-pixvisualize-woo-floatbar .inar-pixvisualize-woo-float-back {
			background: #fff;
			border-color: #c3c4c7;
			color: #2271b1;
		}
		.pixvisualize-woo-floatbar .button .dashicons,
		.inar-pixvisualize-woo-floatbar .button .dashicons {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 16px;
			height: 16px;
			font-size: 16px;
			line-height: 1;
			margin: 0;
			padding: 0;
			flex: none;
			vertical-align: middle;
			transform: none;
		}
		.pixvisualize-woo-floatbar .button .pixvisualize-woo-float-label,
		.inar-pixvisualize-woo-floatbar .button .inar-pixvisualize-woo-float-label {
			display: inline-flex;
			align-items: center;
			line-height: 1.4;
		}
	</style>

</div>