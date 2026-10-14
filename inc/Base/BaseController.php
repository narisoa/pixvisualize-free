<?php
/**
 * Shared controller: paths, feature toggles and slider renderer.
 *
 * One renderer powers block, shortcode, Elementor and WooCommerce
 * so the front-end stays identical everywhere.
 *
 * @package PixVisualize
 */
namespace Inc\Base;

class BaseController
{
	public $plugin_path;

	public $plugin_url;

	public $plugin;

	public $managers = array();

	public function __construct() {
		$this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
		$this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
		$this->plugin = plugin_basename( dirname( __FILE__, 3 ) ) . '/pixvisualize.php';

		$this->managers = array(
			'pixvisualize_block_manager'       => __( 'Activate PixVisualize Block', 'pixvisualize' ),
			'pixvisualize_elementor_manager'   => __( 'Activate PixVisualize Elementor Widget', 'pixvisualize' ),
			'pixvisualize_shortcode_manager'   => __( 'Activate PixVisualize Shortcodes', 'pixvisualize' ),
			'pixvisualize_woocommerce_manager' => __( 'Activate PixVisualize WooCommerce Gallery', 'pixvisualize' ),
		);
	}

	public function activated( string $key )
	{
		$option = get_option( 'pixvisualize' );

		// When the option has never been created (e.g. network/multisite
		// activation where the activation hook does not fire), enable the
		// managers by default instead of silently leaving them disabled.
		if ( ! is_array( $option ) ) {
			return true;
		}

		return isset( $option[ $key ] ) ? $option[ $key ] : true;
	}

	/**
	 * Render the comparison slider (block, shortcode, Elementor, WooCommerce).
	 *
	 * @param array $atts Slider settings.
	 * @return string
	 */
	protected function buildSliderMarkup( array $atts )
	{
		$atts = wp_parse_args(
			$atts,
			array(
				'before'               => '',
				'after'                => '',
				'position'             => 50,
				'before_label'         => 'Before',
				'after_label'          => 'After',
				'show_labels'          => 1,
				'orientation'          => 'horizontal',
				'height'               => 450,
				'ratio'                => '16/9',
				'full_width'           => 0,
				'handle'               => 'circle',
				'svg'                  => '',
				'handle_size'          => 24,
				'handle_bg'            => '',
				'handle_arrow_color'   => '#666666',
				'handle_padding'       => 0,
				'label_position_before' => 'top-left',
				'label_position_after'  => 'top-right',
				'label_bg'             => '#00000080',
				'label_color'          => '#ffffff',
				'label_font_size'      => 12,
				'label_font_weight'    => 600,
				'label_padding_x'      => 10,
				'label_padding_y'      => 5,
				'label_border_radius'  => 4,
				'separator_thickness'  => 2,
				'separator_color'      => '#ffffff',
				'separator_shadow'     => 1,
				'hover'                => 0,
				'autoscan'             => 0,
				'autoscan_duration'    => 2500,
				'magnifier'            => 0,
				'magnifier_zoom'       => 2,
				'tablet_position'      => 50,
				'mobile_position'      => 50,
				'tablet_ratio'         => '',
				'mobile_ratio'         => '',
				'alignment'            => 'center',
				'caption_title'          => '',
				'caption_text'           => '',
				'show_caption'           => 1,
				'caption_bg'             => '#ffffff',
				'caption_title_color'    => '#1e293b',
				'caption_text_color'     => '#64748b',
				'caption_alignment'      => 'center',
				'caption_title_size'     => 22,
				'caption_text_size'      => 15,
				'caption_padding'        => 16,
				'caption_padding_x'      => 5,
				'caption_padding_y'      => 9,
				'container_shadow'             => 0,
				'container_shadow_color'       => '#000000',
				'container_shadow_horizontal'  => 0,
				'container_shadow_vertical'    => 8,
				'container_shadow_blur'        => 20,
				'container_shadow_spread'      => 0,
				'container_shadow_opacity'      => 35,
				'container_border_width'       => 0,
				// Free presets: 2px gray border, 16px radius. Pro settings win when set.
				'container_border'             => 0,
				'container_rounded'            => 0,
				'container_border_color'       => '#000000',
				'container_border_style'       => 'solid',
				'container_border_radius_tl'   => 0,
				'container_border_radius_tr'   => 0,
				'container_border_radius_br'   => 0,
				'container_border_radius_bl'   => 0,
				'extra_class'                 => '',
			)
		);

		$blockId = 'pixvisualize-block-' . uniqid();

		$output  = '<div class="pixvisualize-block-wrapper' . ( $atts['extra_class'] ? ' ' . esc_attr( $atts['extra_class'] ) : '' ) . '"';
		$output .= ' data-before-image="' . esc_attr( $atts['before'] ) . '"';
		$output .= ' data-after-image="' . esc_attr( $atts['after'] ) . '"';
		$output .= ' data-initial-position="' . esc_attr( $atts['position'] ) . '"';
		$output .= ' data-before-label="' . esc_attr( $atts['before_label'] ) . '"';
		$output .= ' data-after-label="' . esc_attr( $atts['after_label'] ) . '"';
		$output .= ' data-show-labels="' . ( $atts['show_labels'] ? '1' : '0' ) . '"';
		$output .= ' data-aspect-ratio="' . esc_attr( $atts['ratio'] ) . '"';
		$output .= ' data-height="' . esc_attr( $atts['height'] ) . '"';
		$output .= ' data-full-width="' . ( $atts['full_width'] ? '1' : '0' ) . '"';
		$output .= ' data-orientation="' . esc_attr( $atts['orientation'] ) . '"';
		$output .= ' data-handle-style="' . esc_attr( $atts['handle'] ) . '"';
		$output .= ' data-handle-icon-svg="' . esc_attr( $atts['svg'] ) . '"';
		$output .= ' data-handle-size="' . esc_attr( $atts['handle_size'] ) . '"';
		$output .= ' data-handle-bg="' . esc_attr( $atts['handle_bg'] ) . '"';
		$output .= ' data-handle-arrow-color="' . esc_attr( $atts['handle_arrow_color'] ) . '"';
		$output .= ' data-handle-padding="' . esc_attr( $atts['handle_padding'] ) . '"';
		$output .= ' data-label-position-before="' . esc_attr( $atts['label_position_before'] ) . '"';
		$output .= ' data-label-position-after="' . esc_attr( $atts['label_position_after'] ) . '"';
		$output .= ' data-label-background="' . esc_attr( $atts['label_bg'] ) . '"';
		$output .= ' data-label-color="' . esc_attr( $atts['label_color'] ) . '"';
		$output .= ' data-label-font-size="' . esc_attr( $atts['label_font_size'] ) . '"';
		$output .= ' data-label-font-weight="' . esc_attr( $atts['label_font_weight'] ) . '"';
		$output .= ' data-label-padding-x="' . esc_attr( $atts['label_padding_x'] ) . '"';
		$output .= ' data-label-padding-y="' . esc_attr( $atts['label_padding_y'] ) . '"';
		$output .= ' data-label-border-radius="' . esc_attr( $atts['label_border_radius'] ) . '"';
		$output .= ' data-separator-thickness="' . esc_attr( $atts['separator_thickness'] ) . '"';
		$output .= ' data-separator-color="' . esc_attr( $atts['separator_color'] ) . '"';
		$output .= ' data-separator-shadow="' . ( $atts['separator_shadow'] ? '1' : '0' ) . '"';
		$output .= ' data-hover-mode="' . ( $atts['hover'] ? '1' : '0' ) . '"';
		$output .= ' data-auto-scan="' . ( $atts['autoscan'] ? '1' : '0' ) . '"';
		$output .= ' data-auto-scan-duration="' . esc_attr( $atts['autoscan_duration'] ) . '"';
		$output .= ' data-magnifier="' . ( $atts['magnifier'] ? '1' : '0' ) . '"';
		$output .= ' data-magnifier-zoom="' . esc_attr( $atts['magnifier_zoom'] ) . '"';
		$output .= ' data-tablet-position="' . esc_attr( $atts['tablet_position'] ) . '"';
		$output .= ' data-mobile-position="' . esc_attr( $atts['mobile_position'] ) . '"';
		$output .= ' data-tablet-ratio="' . esc_attr( $atts['tablet_ratio'] ) . '"';
		$output .= ' data-mobile-ratio="' . esc_attr( $atts['mobile_ratio'] ) . '"';
		$output .= ' data-container-shadow="' . ( $atts['container_shadow'] ? '1' : '0' ) . '"';
		$output .= ' data-container-shadow-color="' . esc_attr( $atts['container_shadow_color'] ) . '"';
		$output .= ' data-container-shadow-horizontal="' . esc_attr( $atts['container_shadow_horizontal'] ) . '"';
		$output .= ' data-container-shadow-vertical="' . esc_attr( $atts['container_shadow_vertical'] ) . '"';
		$output .= ' data-container-shadow-blur="' . esc_attr( $atts['container_shadow_blur'] ) . '"';
		$output .= ' data-container-shadow-spread="' . esc_attr( $atts['container_shadow_spread'] ) . '"';
		$output .= ' data-container-shadow-opacity="' . esc_attr( $atts['container_shadow_opacity'] ) . '"';
		$output .= ' data-container-border-width="' . esc_attr( $atts['container_border_width'] ) . '"';
		$output .= ' data-container-border="' . ( $atts['container_border'] ? '1' : '0' ) . '"';
		$output .= ' data-container-border-color="' . esc_attr( $atts['container_border_color'] ) . '"';
		$output .= ' data-container-border-style="' . esc_attr( $atts['container_border_style'] ) . '"';
		$output .= ' data-container-border-radius-tl="' . esc_attr( $atts['container_border_radius_tl'] ) . '"';
		$output .= ' data-container-border-radius-tr="' . esc_attr( $atts['container_border_radius_tr'] ) . '"';
		$output .= ' data-container-border-radius-br="' . esc_attr( $atts['container_border_radius_br'] ) . '"';
		$output .= ' data-container-border-radius-bl="' . esc_attr( $atts['container_border_radius_bl'] ) . '"';
		$output .= ' data-container-rounded="' . ( $atts['container_rounded'] ? '1' : '0' ) . '"';
		$output .= ' data-alignment="' . esc_attr( $atts['alignment'] ) . '"';
		$caption_alignment = in_array( $atts['caption_alignment'], array( 'left', 'center', 'right' ), true ) ? $atts['caption_alignment'] : 'center';
		$output .= ' data-caption-title="' . esc_attr( $atts['caption_title'] ) . '"';
		$output .= ' data-caption-text="' . esc_attr( $atts['caption_text'] ) . '"';
		$output .= ' data-show-caption="' . ( $atts['show_caption'] ? '1' : '0' ) . '"';
		$output .= ' data-caption-bg="' . esc_attr( $atts['caption_bg'] ) . '"';
		$output .= ' data-caption-title-color="' . esc_attr( $atts['caption_title_color'] ) . '"';
		$output .= ' data-caption-text-color="' . esc_attr( $atts['caption_text_color'] ) . '"';
		$output .= ' data-caption-alignment="' . esc_attr( $caption_alignment ) . '"';
		$output .= ' data-caption-title-size="' . esc_attr( $atts['caption_title_size'] ) . '"';
		$output .= ' data-caption-text-size="' . esc_attr( $atts['caption_text_size'] ) . '"';
		$output .= ' data-caption-padding="' . esc_attr( $atts['caption_padding'] ) . '"';
		// Separate inner padding (Y drives caption height). Legacy fallback.
		$caption_padding_x = ( null !== $atts['caption_padding_x'] && '' !== $atts['caption_padding_x'] ) ? $atts['caption_padding_x'] : $atts['caption_padding'];
		$caption_padding_y = ( null !== $atts['caption_padding_y'] && '' !== $atts['caption_padding_y'] ) ? $atts['caption_padding_y'] : $atts['caption_padding'];
		$output .= ' data-caption-padding-x="' . esc_attr( $caption_padding_x ) . '"';
		$output .= ' data-caption-padding-y="' . esc_attr( $caption_padding_y ) . '"';
		$output .= ' id="' . esc_attr( $blockId ) . '">';
		$output .= '<div class="pixvisualize-react-root"></div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Enqueue front-end slider assets for any placement.
	 *
	 * @return void
	 */
	protected function enqueueViewAssets()
	{
		if ( ! wp_script_is( 'create-block-pixvisualize-view', 'registered' ) ) {
			$asset_file = $this->plugin_path . 'build/pixvisualize/view.asset.php';

			if ( ! file_exists( $asset_file ) ) {
				return;
			}

			$asset = include $asset_file;

			wp_register_script(
				'create-block-pixvisualize-view',
				$this->plugin_url . 'build/pixvisualize/view.js',
				$asset['dependencies'],
				$asset['version'],
				true
			);

			wp_register_style(
				'create-block-pixvisualize-style',
				$this->plugin_url . 'build/pixvisualize/style-index.css',
				array(),
				$asset['version']
			);
		}

		wp_enqueue_script( 'create-block-pixvisualize-view' );
		wp_enqueue_style( 'create-block-pixvisualize-style' );

		// Front-end slider strings (wp.i18n in view.js + shared components):
		// loaded like the editor/admin/woo handles so fallbacks, aria-label
		// and alt texts are localized on the public site too.
		wp_set_script_translations( 'create-block-pixvisualize-view', 'pixvisualize', $this->plugin_path . 'languages/' );
	}
}