<?php
/**
 * @package  PixVisualize
 */
namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * WooCommerce support: Before/After gallery on single product pages.
 *
 * The full comparator configuration (images, ratio, orientation, labels,
 * etc.) lives in a native "PixVisualize" tab inside the WooCommerce Product
 * Data panel. A lightweight sidebar metabox keeps a quick preview + guide
 * and allows fast image selection; both locations read and update the same
 * postmeta keys, kept in sync by an admin script.
 *
 * When both images exist, the exact same slider wrapper as the Gutenberg
 * block is rendered above the product gallery, so the React view script
 * hydrates it like any other instance.
 *
 * All hooks are conditional on the 'product' post type existing, so the
 * store is never broken when WooCommerce is absent or inactive.
 */
class WooCommerceController extends BaseController
{
	const META_BEFORE               = '_pixvisualize_before_image';
	const META_AFTER                = '_pixvisualize_after_image';
	const META_RATIO                = '_pixvisualize_ratio';
	const META_ORIENTATION          = '_pixvisualize_orientation';
	const META_HEIGHT               = '_pixvisualize_height';
	const META_POSITION             = '_pixvisualize_position';
	const META_BEFORE_LABEL         = '_pixvisualize_before_label';
	const META_AFTER_LABEL          = '_pixvisualize_after_label';
	const META_SHOW_LABELS          = '_pixvisualize_show_labels';
	const META_FULL_WIDTH           = '_pixvisualize_full_width';
	const META_HANDLE               = '_pixvisualize_handle';
	const META_HANDLE_SVG           = '_pixvisualize_handle_svg';
	const META_HANDLE_SIZE          = '_pixvisualize_handle_size';
	const META_HANDLE_BG            = '_pixvisualize_handle_background';
	const META_HANDLE_ARROW_COLOR   = '_pixvisualize_handle_arrow_color';
	const META_HANDLE_PADDING       = '_pixvisualize_handle_padding';
	const META_LABEL_POS_BEFORE     = '_pixvisualize_label_position_before';
	const META_LABEL_POS_AFTER      = '_pixvisualize_label_position_after';
	const META_TABLET_POSITION      = '_pixvisualize_tablet_position';
	const META_MOBILE_POSITION      = '_pixvisualize_mobile_position';
	const META_TABLET_RATIO         = '_pixvisualize_tablet_ratio';
	const META_MOBILE_RATIO         = '_pixvisualize_mobile_ratio';
	const META_ALIGNMENT            = '_pixvisualize_alignment';
	const META_SEPARATOR_THICKNESS  = '_pixvisualize_separator_thickness';
	const META_SEPARATOR_COLOR      = '_pixvisualize_separator_color';
	const META_SEPARATOR_SHADOW     = '_pixvisualize_separator_shadow';
	const META_LABEL_BG             = '_pixvisualize_label_background';
	const META_LABEL_COLOR          = '_pixvisualize_label_color';
	const META_LABEL_FONT_SIZE      = '_pixvisualize_label_font_size';
	const META_LABEL_FONT_WEIGHT    = '_pixvisualize_label_font_weight';
	const META_LABEL_PADDING_X      = '_pixvisualize_label_padding_x';
	const META_LABEL_PADDING_Y      = '_pixvisualize_label_padding_y';
	const META_LABEL_BORDER_RADIUS  = '_pixvisualize_label_border_radius';
	const META_HOVER                = '_pixvisualize_hover_mode';
	const META_AUTOSCAN             = '_pixvisualize_auto_scan';
	const META_AUTOSCAN_DURATION    = '_pixvisualize_auto_scan_duration';
	const META_MAGNIFIER            = '_pixvisualize_magnifier';
	const META_MAGNIFIER_ZOOM       = '_pixvisualize_magnifier_zoom';
	const META_CONTAINER_SHADOW           = '_pixvisualize_container_shadow';
	const META_CONTAINER_SHADOW_COLOR     = '_pixvisualize_container_shadow_color';
	const META_CONTAINER_SHADOW_HORIZONTAL = '_pixvisualize_container_shadow_horizontal';
	const META_CONTAINER_SHADOW_VERTICAL  = '_pixvisualize_container_shadow_vertical';
	const META_CONTAINER_SHADOW_BLUR      = '_pixvisualize_container_shadow_blur';
	const META_CONTAINER_SHADOW_SPREAD    = '_pixvisualize_container_shadow_spread';
	const META_CONTAINER_SHADOW_OPACITY   = '_pixvisualize_container_shadow_opacity';
	const META_CONTAINER_BORDER_WIDTH     = '_pixvisualize_container_border_width';
	// FREE toggles (checkboxes): default 2px solid gray border, default 16px radius.
	const META_CONTAINER_BORDER         = '_pixvisualize_container_border';
	const META_CONTAINER_ROUNDED        = '_pixvisualize_container_rounded';
	const META_CONTAINER_BORDER_COLOR     = '_pixvisualize_container_border_color';
	const META_CONTAINER_BORDER_STYLE     = '_pixvisualize_container_border_style';
	const META_CONTAINER_BORDER_RADIUS_TL = '_pixvisualize_container_border_radius_tl';
	const META_CONTAINER_BORDER_RADIUS_TR = '_pixvisualize_container_border_radius_tr';
	const META_CONTAINER_BORDER_RADIUS_BR = '_pixvisualize_container_border_radius_br';
	const META_CONTAINER_BORDER_RADIUS_BL = '_pixvisualize_container_border_radius_bl';
	const META_CAPTION_TITLE         = '_pixvisualize_caption_title';
	const META_CAPTION_TEXT          = '_pixvisualize_caption_text';
	const META_SHOW_CAPTION          = '_pixvisualize_show_caption';
	const META_CAPTION_BG            = '_pixvisualize_caption_bg';
	const META_CAPTION_TITLE_COLOR   = '_pixvisualize_caption_title_color';
	const META_CAPTION_TEXT_COLOR    = '_pixvisualize_caption_text_color';
	const META_CAPTION_ALIGNMENT     = '_pixvisualize_caption_alignment';
	const META_CAPTION_TITLE_SIZE    = '_pixvisualize_caption_title_size';
	const META_CAPTION_TEXT_SIZE     = '_pixvisualize_caption_text_size';
	const META_CAPTION_PADDING       = '_pixvisualize_caption_padding';
	const META_CAPTION_PADDING_X     = '_pixvisualize_caption_padding_x';
	const META_CAPTION_PADDING_Y     = '_pixvisualize_caption_padding_y';
	const META_PLACEMENT            = '_pixvisualize_placement';
	const META_TAB_TITLE            = '_pixvisualize_tab_title';
	const META_DESC                 = '_pixvisualize_desc';
	const META_DESC_LAYOUT          = '_pixvisualize_desc_layout';
	// Legacy key (former "Description alignment" select, now handled by the
	// editor toolbar): no longer saved, deleted on product save.
	const META_DESC_ALIGN           = '_pixvisualize_desc_alignment';
	const META_DESC_VALIGN          = '_pixvisualize_desc_vertical';

	// Legacy description fields, merged into META_DESC on first save.
	const META_TAB_DESC_BEFORE      = '_pixvisualize_tab_desc_before';
	const META_TAB_DESC_AFTER       = '_pixvisualize_tab_desc_after';

	/**
	 * Allowed values used by the front-end slider.
	 */
	const RATIOS         = array( '21/9', '2/1', '16/9', '3/2', '4/3', '1/1', '3/4', '2/3', '9/16', '1/2', '9/21' );
	const ORIENTATIONS   = array( 'horizontal', 'vertical' );
	const HANDLES        = array( 'circle', 'transparent', 'line', 'arrows', 'custom' );
	const PLACEMENTS     = array(
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
	);
	const LABEL_POSITIONS = array( 'top-left', 'top-right', 'bottom-left', 'bottom-right' );
	const ALIGNMENTS     = array( 'left', 'center', 'right' );
	const DESC_LAYOUTS   = array( 'top', 'bottom', 'left', 'right' );
	const DESC_VALIGNS   = array( 'top', 'middle', 'bottom' );

	/**
	 * Placement => WooCommerce hook + candidate template files that fire it.
	 *
	 * A placement only works when the active theme fires its hook. Core
	 * WooCommerce fires all of these; a theme breaks a placement by
	 * overriding one of the candidate files WITHOUT the hook (or, for
	 * block themes, by not using classic templates at all).
	 */
	const PLACEMENT_HOOKS = array(
		'above_gallery'              => array( 'hook' => 'woocommerce_before_single_product', 'files' => array( 'single-product.php' ) ),
		'above_main_image'           => array( 'hook' => 'woocommerce_before_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'under_thumbnails'           => array( 'hook' => 'woocommerce_product_thumbnails', 'files' => array( 'single-product/product-thumbnails.php' ) ),
		'summary_before_title'       => array( 'hook' => 'woocommerce_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'summary_after_title'        => array( 'hook' => 'woocommerce_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'summary_after_price'        => array( 'hook' => 'woocommerce_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'summary_after_excerpt'      => array( 'hook' => 'woocommerce_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'summary_after_cart'         => array( 'hook' => 'woocommerce_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'summary_end'                => array( 'hook' => 'woocommerce_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'after_summary_before_tabs'  => array( 'hook' => 'woocommerce_after_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'after_summary_after_tabs'   => array( 'hook' => 'woocommerce_after_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'after_summary_after_related' => array( 'hook' => 'woocommerce_after_single_product_summary', 'files' => array( 'content-single-product.php', 'single-product.php' ) ),
		'after_single_product'       => array( 'hook' => 'woocommerce_after_single_product', 'files' => array( 'single-product.php' ) ),
		'product_tab'                => array( 'hook' => 'woocommerce_product_tabs', 'files' => array( 'single-product/tabs/tabs.php' ) ),
	);
	const BORDER_STYLES  = array( 'solid', 'dashed', 'dotted', 'double' );

	public function register()
	{
		if ( ! $this->activated( 'pixvisualize_woocommerce_manager' ) ) {
			return;
		}

		// The 'product' post type is only registered by WooCommerce on init,
		// while this plugin's services bootstrap at plugin load (before init).
		// Defer the hooks to init so the CPT always exists when we check it.
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Register the WooCommerce hooks once the 'product' post type is known.
	 * Never break the shop: bail as soon as WooCommerce (CPT 'product')
	 * is not available.
	 */
	public function init()
	{
		if ( ! post_type_exists( 'product' ) ) {
			return;
		}

		add_action( 'add_meta_boxes', array( $this, 'addMetaBoxes' ) );
		add_action( 'save_post_product', array( $this, 'saveProduct' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminAssets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontendAssets' ) );

		// Placement hooks. Each wrapper only renders when the product's saved
		// placement matches, so the same renderer serves every position.
		add_action( 'woocommerce_before_single_product', array( $this, 'renderAboveGallery' ) );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'renderAboveMainImage' ), 5 );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'renderUnderThumbnails' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'renderSummaryBeforeTitle' ), 5 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'renderSummaryAfterTitle' ), 10 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'renderSummaryAfterPrice' ), 15 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'renderSummaryAfterExcerpt' ), 25 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'renderSummaryAfterCart' ), 35 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'renderSummaryEnd' ), 60 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'renderAfterSummaryBeforeTabs' ), 5 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'renderAfterSummaryAfterTabs' ), 12 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'renderAfterSummaryAfterRelated' ), 25 );
		add_action( 'woocommerce_after_single_product', array( $this, 'renderAfterSingleProduct' ) );
		add_filter( 'woocommerce_product_tabs', array( $this, 'addComparisonTab' ) );

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'addProductDataTab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'renderProductDataPanel' ) );
	}

	/**
	 * Register the sidebar metabox (quick preview + guide).
	 */
	public function addMetaBoxes()
	{
		add_meta_box(
			'pixvisualize_product_comparator',
			__( 'PixVisualize — Comparateur produit', 'pixvisualize' ),
			array( $this, 'renderMetaBox' ),
			'product',
			'side',
			'default'
		);
	}

	/**
	 * Render the sidebar metabox.
	 *
	 * @param \WP_Post $post Current product post object.
	 */
	public function renderMetaBox( $post )
	{
		include $this->plugin_path . 'templates/woocommerce.php';
	}

	/**
	 * Add the "PixVisualize" tab to the WooCommerce Product Data panel.
	 *
	 * @param  array $tabs Registered product data tabs.
	 * @return array       Tabs with the PixVisualize tab appended.
	 */
	public function addProductDataTab( $tabs )
	{
		$tabs['pixvisualize'] = array(
			'label'    => __( 'PixVisualize', 'pixvisualize' ),
			'target'   => 'pixvisualizeduct-data',
			'class'    => array(),
			'priority' => 40,
		);

		return $tabs;
	}

	/**
	 * Render the PixVisualize product data panel (full configuration).
	 */
	public function renderProductDataPanel()
	{
		global $post;

		if ( ! $post || 'product' !== $post->post_type ) {
			return;
		}

		$unavailable_placements = $this->getUnavailablePlacements();

		include $this->plugin_path . 'templates/woocommerce-product-data.php';
	}

	/**
	 * Placements the active theme cannot display (hidden from the select).
	 *
	 * Scans the theme for WooCommerce template overrides missing the hook
	 * each placement relies on. Only a handful of small files are read, on
	 * the product editor screen, with the result cached in a transient keyed
	 * by theme + version — negligible cost. Block themes fire none of the
	 * classic hooks, so every placement is reported unavailable there.
	 *
	 * Developers can override the result (false negative escape hatch):
	 * `add_filter( 'pixvisualize_woo_unavailable_placements', fn() => array() );`
	 *
	 * @return string[] Unavailable placement keys.
	 */
	public function getUnavailablePlacements()
	{
		$theme = wp_get_theme();
		$key   = 'pixvisualize_woo_hooks_' . md5( get_stylesheet() . '|' . $theme->get( 'Version' ) );

		$cached = get_transient( $key );

		if ( is_array( $cached ) ) {
			return apply_filters( 'pixvisualize_woo_unavailable_placements', $cached );
		}

		$unavailable = array();

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			$unavailable = self::PLACEMENTS;
		} else {
			foreach ( self::PLACEMENT_HOOKS as $placement => $info ) {
				if ( ! $this->themeFiresHook( $info['hook'], $info['files'] ) ) {
					$unavailable[] = $placement;
				}
			}
		}

		set_transient( $key, $unavailable, DAY_IN_SECONDS );

		return apply_filters( 'pixvisualize_woo_unavailable_placements', $unavailable );
	}

	/**
	 * Check whether the active theme fires a WooCommerce hook.
	 *
	 * Reads the effective template (theme override when present, core file
	 * otherwise) for each candidate file, plus the theme functions.php files
	 * (hooks fired from custom template parts). No override anywhere means
	 * core is intact and the hook fires.
	 *
	 * @param  string   $hook       Hook name, e.g. 'woocommerce_before_single_product'.
	 * @param  string[] $candidates WooCommerce template paths, e.g. array( 'single-product.php' ).
	 * @return bool
	 */
	private function themeFiresHook( $hook, $candidates )
	{
		$overrides = array();

		foreach ( $candidates as $file ) {
			$located = locate_template( 'woocommerce/' . $file );

			if ( $located ) {
				$overrides[ $file ] = $located;
			}
		}

		if ( ! $overrides ) {
			return true;
		}

		foreach ( $overrides as $located ) {
			if ( $this->fileFiresHook( $located, $hook ) ) {
				return true;
			}
		}

		// Hook moved to a custom template part loaded from functions.php.
		foreach ( array( get_stylesheet_directory() . '/functions.php', get_template_directory() . '/functions.php' ) as $functions_file ) {
			if ( $this->fileFiresHook( $functions_file, $hook ) ) {
				return true;
			}
		}

		// Last chance: a non-overridden candidate still loads the core file.
		foreach ( $candidates as $file ) {
			if ( isset( $overrides[ $file ] ) ) {
				continue;
			}

			$core_file = $this->getCoreTemplateFile( $file );

			if ( $core_file && $this->fileFiresHook( $core_file, $hook ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether a PHP file fires a hook (do_action / apply_filters).
	 *
	 * @param  string $path File path.
	 * @param  string $hook Hook name.
	 * @return bool
	 */
	private function fileFiresHook( $path, $hook )
	{
		if ( ! $path || ! is_readable( $path ) ) {
			return false;
		}

		$contents = file_get_contents( $path );

		if ( false === $contents ) {
			return false;
		}

		return (bool) preg_match(
			'/(do_action|apply_filters)\s*\(\s*[\'"]' . preg_quote( $hook, '/' ) . '[\'"]/',
			$contents
		);
	}

	/**
	 * Core WooCommerce template file path.
	 *
	 * @param  string $file Template path relative to woocommerce/templates/.
	 * @return string|false
	 */
	private function getCoreTemplateFile( $file )
	{
		if ( defined( 'WC_ABSPATH' ) ) {
			$path = WC_ABSPATH . 'templates/' . $file;
		} else {
			$path = WP_PLUGIN_DIR . '/woocommerce/templates/' . $file;
		}

		return is_readable( $path ) ? $path : false;
	}

	/**
	 * Persist all PixVisualize product settings on product save.
	 *
	 * @param int $post_id Product post ID.
	 */
	public function saveProduct( $post_id )
	{
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['pixvisualize_product_nonce'] )
			|| ! wp_verify_nonce( $_POST['pixvisualize_product_nonce'], 'pixvisualize_product_save' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$this->saveMetaInt( $post_id, 'pixvisualize_woo_before_id', self::META_BEFORE, 0, 0, PHP_INT_MAX );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_after_id', self::META_AFTER, 0, 0, PHP_INT_MAX );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_ratio', self::META_RATIO, self::RATIOS, '16/9' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_orientation', self::META_ORIENTATION, self::ORIENTATIONS, 'horizontal' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_placement', self::META_PLACEMENT, self::PLACEMENTS, 'above_gallery' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_handle', self::META_HANDLE, self::HANDLES, 'circle' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_label_position_before', self::META_LABEL_POS_BEFORE, self::LABEL_POSITIONS, 'top-left' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_label_position_after', self::META_LABEL_POS_AFTER, self::LABEL_POSITIONS, 'top-right' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_tablet_ratio', self::META_TABLET_RATIO, self::RATIOS, '' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_mobile_ratio', self::META_MOBILE_RATIO, self::RATIOS, '' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_alignment', self::META_ALIGNMENT, self::ALIGNMENTS, 'center' );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_height', self::META_HEIGHT, 450, 50, 2000 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_position', self::META_POSITION, 50, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_tablet_position', self::META_TABLET_POSITION, 50, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_mobile_position', self::META_MOBILE_POSITION, 50, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_separator_thickness', self::META_SEPARATOR_THICKNESS, 2, 1, 10 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_handle_size', self::META_HANDLE_SIZE, 24, 16, 64 );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_handle_bg', self::META_HANDLE_BG, '' );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_handle_arrow_color', self::META_HANDLE_ARROW_COLOR, '#666666' );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_handle_padding', self::META_HANDLE_PADDING, 0, 0, 20 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_label_font_size', self::META_LABEL_FONT_SIZE, 12, 10, 24 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_label_font_weight', self::META_LABEL_FONT_WEIGHT, 600, 400, 900 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_label_padding_x', self::META_LABEL_PADDING_X, 10, 0, 30 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_label_padding_y', self::META_LABEL_PADDING_Y, 5, 0, 30 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_label_border_radius', self::META_LABEL_BORDER_RADIUS, 4, 0, 20 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_auto_scan_duration', self::META_AUTOSCAN_DURATION, 2500, 1000, 10000 );
		$this->saveMetaFloat( $post_id, 'pixvisualize_woo_magnifier_zoom', self::META_MAGNIFIER_ZOOM, 2, 1.5, 4 );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_separator_color', self::META_SEPARATOR_COLOR, '#ffffff' );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_label_background', self::META_LABEL_BG, '#00000080' );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_label_color', self::META_LABEL_COLOR, '#ffffff' );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_container_shadow', self::META_CONTAINER_SHADOW );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_container_shadow_color', self::META_CONTAINER_SHADOW_COLOR, '#000000' );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_shadow_horizontal', self::META_CONTAINER_SHADOW_HORIZONTAL, 0, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_shadow_vertical', self::META_CONTAINER_SHADOW_VERTICAL, 8, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_shadow_blur', self::META_CONTAINER_SHADOW_BLUR, 20, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_shadow_spread', self::META_CONTAINER_SHADOW_SPREAD, 0, -50, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_shadow_opacity', self::META_CONTAINER_SHADOW_OPACITY, 35, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_border_width', self::META_CONTAINER_BORDER_WIDTH, 0, 0, 20 );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_container_border', self::META_CONTAINER_BORDER );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_container_rounded', self::META_CONTAINER_ROUNDED );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_container_border_color', self::META_CONTAINER_BORDER_COLOR, '#000000' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_container_border_style', self::META_CONTAINER_BORDER_STYLE, self::BORDER_STYLES, 'solid' );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_border_radius_tl', self::META_CONTAINER_BORDER_RADIUS_TL, 0, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_border_radius_tr', self::META_CONTAINER_BORDER_RADIUS_TR, 0, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_border_radius_br', self::META_CONTAINER_BORDER_RADIUS_BR, 0, 0, 100 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_container_border_radius_bl', self::META_CONTAINER_BORDER_RADIUS_BL, 0, 0, 100 );
		$this->saveMetaText( $post_id, 'pixvisualize_woo_before_label', self::META_BEFORE_LABEL, 'Before' );
		$this->saveMetaText( $post_id, 'pixvisualize_woo_after_label', self::META_AFTER_LABEL, 'After' );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_show_caption', self::META_SHOW_CAPTION );
		$this->saveMetaText( $post_id, 'pixvisualize_woo_caption_title', self::META_CAPTION_TITLE, '' );
		$this->saveMetaText( $post_id, 'pixvisualize_woo_caption_text', self::META_CAPTION_TEXT, '' );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_caption_bg', self::META_CAPTION_BG, '#ffffff' );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_caption_title_color', self::META_CAPTION_TITLE_COLOR, '#1e293b' );
		$this->saveMetaColor( $post_id, 'pixvisualize_woo_caption_text_color', self::META_CAPTION_TEXT_COLOR, '#64748b' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_caption_alignment', self::META_CAPTION_ALIGNMENT, self::ALIGNMENTS, 'center' );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_caption_title_size', self::META_CAPTION_TITLE_SIZE, 22, 12, 48 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_caption_text_size', self::META_CAPTION_TEXT_SIZE, 15, 10, 32 );
		$this->saveMetaInt( $post_id, 'pixvisualize_woo_caption_padding', self::META_CAPTION_PADDING, 16, 0, 60 );
		$this->saveMetaIntWithLegacyFallback( $post_id, 'pixvisualize_woo_caption_padding_x', self::META_CAPTION_PADDING_X, self::META_CAPTION_PADDING, 5, 0, 60 );
		$this->saveMetaIntWithLegacyFallback( $post_id, 'pixvisualize_woo_caption_padding_y', self::META_CAPTION_PADDING_Y, self::META_CAPTION_PADDING, 9, 0, 60 );
		$this->saveMetaSvg( $post_id, 'pixvisualize_woo_handle_svg', self::META_HANDLE_SVG );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_show_labels', self::META_SHOW_LABELS );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_full_width', self::META_FULL_WIDTH );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_separator_shadow', self::META_SEPARATOR_SHADOW );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_hover_mode', self::META_HOVER );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_auto_scan', self::META_AUTOSCAN );
		$this->saveMetaBool( $post_id, 'pixvisualize_woo_magnifier', self::META_MAGNIFIER );
		$this->saveMetaText( $post_id, 'pixvisualize_woo_tab_title', self::META_TAB_TITLE, 'Comparison' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_desc_layout', self::META_DESC_LAYOUT, self::DESC_LAYOUTS, 'top' );
		$this->saveMetaChoice( $post_id, 'pixvisualize_woo_desc_vertical', self::META_DESC_VALIGN, self::DESC_VALIGNS, 'middle' );
		// Cleanup of the removed "Description alignment" setting.
		delete_post_meta( $post_id, self::META_DESC_ALIGN );
		$this->saveMetaDesc( $post_id, 'pixvisualize_woo_desc', self::META_DESC );
	}

	/**
	 * Save an integer meta field.
	 *
	 * Image IDs are special: 0 means "no image" and removes the meta key.
	 * Other integer settings keep their (clamped) value, 0 included.
	 */
	private function saveMetaInt( $post_id, $field, $meta_key, $default, $min, $max )
	{
		$value = isset( $_POST[ $field ] ) ? absint( $_POST[ $field ] ) : $default;
		$value = max( $min, min( $max, $value ) );

		if ( self::META_BEFORE === $meta_key || self::META_AFTER === $meta_key ) {
			if ( $value > 0 ) {
				update_post_meta( $post_id, $meta_key, $value );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
			return;
		}

		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Save an integer meta with legacy fallback (caption padding X/Y).
	 *
	 * When the new field is absent from $_POST (old product edited with an
	 * outdated template), fall back to the legacy uniform padding meta so
	 * existing products keep their spacing instead of resetting to 5/9.
	 */
	private function saveMetaIntWithLegacyFallback( $post_id, $field, $meta_key, $legacy_meta_key, $default, $min, $max )
	{
		if ( isset( $_POST[ $field ] ) ) {
			$value = max( $min, min( $max, absint( $_POST[ $field ] ) ) );
		} else {
			$legacy = get_post_meta( $post_id, $legacy_meta_key, true );
			$value  = ( '' === $legacy || false === $legacy ) ? $default : max( $min, min( $max, absint( $legacy ) ) );
		}

		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Save a meta field restricted to a whitelist of allowed values.
	 */
	private function saveMetaChoice( $post_id, $field, $meta_key, $allowed, $default )
	{
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : $default;

		if ( ! in_array( $value, $allowed, true ) ) {
			$value = $default;
		}

		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Save a free-text meta field.
	 */
	private function saveMetaText( $post_id, $field, $meta_key, $default )
	{
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : $default;

		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Save a hex color meta field (6 or 8 digit hex, alpha included).
	 */
	private function saveMetaColor( $post_id, $field, $meta_key, $default )
	{
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : $default;

		if ( ! preg_match( '/^#[0-9a-fA-F]{6,8}$/', $value ) ) {
			$value = $default;
		}

		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Save a float meta field within [min, max].
	 */
	private function saveMetaFloat( $post_id, $field, $meta_key, $default, $min, $max )
	{
		$value = isset( $_POST[ $field ] ) ? (float) sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : $default;
		$value = max( $min, min( $max, $value ) );

		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Save the custom SVG handle icon.
	 *
	 * The value is stored as-is (like the block editor stores the custom SVG
	 * inside the post content) and escaped with esc_attr() on output. The
	 * field is only reachable by users who can edit products.
	 */
	private function saveMetaSvg( $post_id, $field, $meta_key )
	{
		$value = isset( $_POST[ $field ] ) ? wp_unslash( $_POST[ $field ] ) : '';

		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Save the single comparison description.
	 *
	 * On first save the legacy "Description Above"/"Description Below" tab
	 * fields are migrated into the unique description, then removed so both
	 * keys never diverge again.
	 */
	private function saveMetaDesc( $post_id, $field, $meta_key )
	{
		$value = isset( $_POST[ $field ] ) ? wp_kses_post( wp_unslash( $_POST[ $field ] ) ) : '';

		if ( '' === trim( (string) $value ) ) {
			$legacy_before = get_post_meta( $post_id, self::META_TAB_DESC_BEFORE, true );
			$legacy_after  = get_post_meta( $post_id, self::META_TAB_DESC_AFTER, true );

			if ( $legacy_before && $legacy_after ) {
				$value = $legacy_before . "\n\n" . $legacy_after;
			} elseif ( $legacy_before ) {
				$value = $legacy_before;
			} elseif ( $legacy_after ) {
				$value = $legacy_after;
			}
		}

		update_post_meta( $post_id, $meta_key, $value );

		delete_post_meta( $post_id, self::META_TAB_DESC_BEFORE );
		delete_post_meta( $post_id, self::META_TAB_DESC_AFTER );
	}

	/**
	 * Save a checkbox meta field as '1' / '0'.
	 */
	private function saveMetaBool( $post_id, $field, $meta_key )
	{
		$value = isset( $_POST[ $field ] ) ? '1' : '0';
		update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Enqueue the wp.media picker + admin script/style on the product
	 * editor screen only.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueueAdminAssets( $hook )
	{
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'product' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();

		// Live preview interactivity (hover / auto-scan / magnifier), the
		// same script that drives the Elementor editor preview.
		wp_enqueue_script(
			'pixvisualize-woocommerce-preview',
			$this->plugin_url . 'assets/js/pixvisualize-elementor-preview.js',
			array(),
			'1.1.0',
			true
		);

		wp_enqueue_script(
			'pixvisualize-woocommerce-metabox',
			$this->plugin_url . 'assets/js/pixvisualize-woocommerce-metabox.js',
			array( 'jquery', 'wp-color-picker', 'wp-i18n' ),
			'1.3.6',
			true
		);
		wp_set_script_translations( 'pixvisualize-woocommerce-metabox', 'pixvisualize', $this->plugin_path . 'languages/' );
		wp_enqueue_style(
			'pixvisualize-woocommerce-metabox',
			$this->plugin_url . 'assets/css/pixvisualize-woocommerce-metabox.css',
			array( 'wp-color-picker' ),
			'1.4.4'
		);
	}

	/**
	 * Register + enqueue the comparison view assets on single product pages.
	 * Registering early lets the block/Elementor handles be reused, while the
	 * render below also re-enqueues for safety during content rendering.
	 */
	public function enqueueFrontendAssets()
	{
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		$this->enqueueViewAssets();
	}

	/**
	 * Placement wrapper: main gallery area, above the gallery.
	 */
	public function renderAboveGallery()
	{
		$this->maybeRender( 'above_gallery' );
	}

	/**
	 * Placement wrapper: main gallery area, under the thumbnails.
	 */
	public function renderUnderThumbnails()
	{
		$this->maybeRender( 'under_thumbnails' );
	}

	/**
	 * Placement wrapper: summary column, before the product title.
	 */
	public function renderSummaryBeforeTitle()
	{
		$this->maybeRender( 'summary_before_title' );
	}

	/**
	 * Placement wrapper: summary column, between the title and the rating.
	 */
	public function renderSummaryAfterTitle()
	{
		$this->maybeRender( 'summary_after_title' );
	}

	/**
	 * Placement wrapper: summary column, under the price.
	 */
	public function renderSummaryAfterPrice()
	{
		$this->maybeRender( 'summary_after_price' );
	}

	/**
	 * Placement wrapper: main gallery area, above the main image.
	 */
	public function renderAboveMainImage()
	{
		$this->maybeRender( 'above_main_image' );
	}

	/**
	 * Placement wrapper: summary column, under the short description.
	 */
	public function renderSummaryAfterExcerpt()
	{
		$this->maybeRender( 'summary_after_excerpt' );
	}

	/**
	 * Placement wrapper: summary column, under the add to cart button.
	 */
	public function renderSummaryAfterCart()
	{
		$this->maybeRender( 'summary_after_cart' );
	}

	/**
	 * Placement wrapper: summary column, at the very end.
	 */
	public function renderSummaryEnd()
	{
		$this->maybeRender( 'summary_end' );
	}

	/**
	 * Placement wrapper: below the summary, before the product tabs.
	 */
	public function renderAfterSummaryBeforeTabs()
	{
		$this->maybeRender( 'after_summary_before_tabs' );
	}

	/**
	 * Placement wrapper: below the summary, between the tabs and the
	 * associated products.
	 */
	public function renderAfterSummaryAfterTabs()
	{
		$this->maybeRender( 'after_summary_after_tabs' );
	}

	/**
	 * Placement wrapper: below the summary, after the related products.
	 */
	public function renderAfterSummaryAfterRelated()
	{
		$this->maybeRender( 'after_summary_after_related' );
	}

	/**
	 * Placement wrapper: very end of the single product page.
	 */
	public function renderAfterSingleProduct()
	{
		$this->maybeRender( 'after_single_product' );
	}

	/**
	 * Add a dedicated "Comparison" tab to the product tabs when the product
	 * placement is set to product tab.
	 *
	 * @param  array $tabs Registered product tabs.
	 * @return array
	 */
	public function addComparisonTab( $tabs )
	{
		if ( 'product_tab' === $this->getPlacement() ) {
			$tabs['pixvisualize'] = array(
				'title'    => $this->getProductMeta( get_the_ID(), self::META_TAB_TITLE, 'Comparison' ),
				'priority' => 50,
				'callback' => array( $this, 'renderProductTab' ),
			);
		}

		return $tabs;
	}

	/**
	 * Tab callback: render the comparison slider inside the dedicated tab.
	 * The optional description and its layout are applied for every
	 * placement, including the product tab.
	 *
	 * @param string $key Tab key (unused).
	 * @param array  $tab Tab definition (unused).
	 */
	public function renderProductTab( $key = '', $tab = array() )
	{
		$this->maybeRender( 'product_tab' );
	}

	/**
	 * Render the comparison slider only when the product's placement matches
	 * the expected value. The default for unconfigured products is the main
	 * gallery area (previous behaviour).
	 *
	 * @param string $expected Expected placement value.
	 */
	private function maybeRender( $expected )
	{
		if ( $expected === $this->getPlacement() ) {
			$this->renderComparison();
		}
	}

	/**
	 * Read the product's page placement with fallback default.
	 *
	 * @return string
	 */
	private function getPlacement()
	{
		$product_id = get_the_ID();
		$placement  = $product_id ? get_post_meta( $product_id, self::META_PLACEMENT, true ) : '';

		return in_array( $placement, self::PLACEMENTS, true ) ? $placement : 'above_gallery';
	}

	/**
	 * Render the comparison slider above the product gallery when the
	 * product has both a Before and an After image.
	 */
	public function renderComparison()
	{
		$product_id = get_the_ID();

		if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
			return;
		}

		$before_id = absint( get_post_meta( $product_id, self::META_BEFORE, true ) );
		$after_id  = absint( get_post_meta( $product_id, self::META_AFTER, true ) );

		if ( ! $before_id || ! $after_id ) {
			return;
		}

		$before_url = wp_get_attachment_image_url( $before_id, 'full' );
		$after_url  = wp_get_attachment_image_url( $after_id, 'full' );

		if ( ! $before_url || ! $after_url ) {
			return;
		}

		$this->enqueueViewAssets();

		$slider = $this->buildSliderMarkup(
			array(
				'before'                => $before_url,
				'after'                 => $after_url,
				'position'              => $this->getProductMeta( $product_id, self::META_POSITION, 50 ),
				'height'                => $this->getProductMeta( $product_id, self::META_HEIGHT, 450 ),
				'ratio'                 => $this->getProductMeta( $product_id, self::META_RATIO, '16/9' ),
				'orientation'           => $this->getProductMeta( $product_id, self::META_ORIENTATION, 'horizontal' ),
				'before_label'          => $this->getProductMeta( $product_id, self::META_BEFORE_LABEL, 'Before' ),
				'after_label'           => $this->getProductMeta( $product_id, self::META_AFTER_LABEL, 'After' ),
				'show_labels'           => $this->getProductMeta( $product_id, self::META_SHOW_LABELS, 1 ),
				'full_width'            => $this->getProductMeta( $product_id, self::META_FULL_WIDTH, 0 ),
				'handle'                => $this->getProductMeta( $product_id, self::META_HANDLE, 'circle' ),
				'svg'                   => $this->getProductMeta( $product_id, self::META_HANDLE_SVG, '' ),
				'handle_size'           => $this->getProductMeta( $product_id, self::META_HANDLE_SIZE, 24 ),
				'handle_bg'             => $this->getProductMeta( $product_id, self::META_HANDLE_BG, '' ),
				'handle_arrow_color'    => $this->getProductMeta( $product_id, self::META_HANDLE_ARROW_COLOR, '#666666' ),
				'handle_padding'        => $this->getProductMeta( $product_id, self::META_HANDLE_PADDING, 0 ),
				'label_position_before' => $this->getProductMeta( $product_id, self::META_LABEL_POS_BEFORE, 'top-left' ),
				'label_position_after'  => $this->getProductMeta( $product_id, self::META_LABEL_POS_AFTER, 'top-right' ),
				'label_bg'              => $this->getProductMeta( $product_id, self::META_LABEL_BG, '#00000080' ),
				'label_color'           => $this->getProductMeta( $product_id, self::META_LABEL_COLOR, '#ffffff' ),
				'label_font_size'       => $this->getProductMeta( $product_id, self::META_LABEL_FONT_SIZE, 12 ),
				'label_font_weight'     => $this->getProductMeta( $product_id, self::META_LABEL_FONT_WEIGHT, 600 ),
				'label_padding_x'       => $this->getProductMeta( $product_id, self::META_LABEL_PADDING_X, 10 ),
				'label_padding_y'       => $this->getProductMeta( $product_id, self::META_LABEL_PADDING_Y, 5 ),
				'label_border_radius'   => $this->getProductMeta( $product_id, self::META_LABEL_BORDER_RADIUS, 4 ),
				'separator_thickness'   => $this->getProductMeta( $product_id, self::META_SEPARATOR_THICKNESS, 2 ),
				'separator_color'       => $this->getProductMeta( $product_id, self::META_SEPARATOR_COLOR, '#ffffff' ),
				'separator_shadow'      => $this->getProductMeta( $product_id, self::META_SEPARATOR_SHADOW, 1 ),
				'hover'                 => $this->getProductMeta( $product_id, self::META_HOVER, 0 ),
				'autoscan'              => $this->getProductMeta( $product_id, self::META_AUTOSCAN, 0 ),
				'autoscan_duration'     => $this->getProductMeta( $product_id, self::META_AUTOSCAN_DURATION, 2500 ),
				'magnifier'             => $this->getProductMeta( $product_id, self::META_MAGNIFIER, 0 ),
				'magnifier_zoom'        => $this->getProductMeta( $product_id, self::META_MAGNIFIER_ZOOM, 2 ),
				'tablet_position'       => $this->getProductMeta( $product_id, self::META_TABLET_POSITION, 50 ),
				'mobile_position'       => $this->getProductMeta( $product_id, self::META_MOBILE_POSITION, 50 ),
				'tablet_ratio'          => $this->getProductMeta( $product_id, self::META_TABLET_RATIO, '' ),
				'mobile_ratio'          => $this->getProductMeta( $product_id, self::META_MOBILE_RATIO, '' ),
				'alignment'             => $this->getProductMeta( $product_id, self::META_ALIGNMENT, 'center' ),
				'extra_class'           => 'pixvisualize-woo-comparator inar-pixvisualize-woo-comparator',
				'caption_title'         => $this->getProductMeta( $product_id, self::META_CAPTION_TITLE, '' ),
				'caption_text'          => $this->getProductMeta( $product_id, self::META_CAPTION_TEXT, '' ),
				'show_caption'          => $this->getProductMeta( $product_id, self::META_SHOW_CAPTION, 1 ),
				'caption_bg'            => $this->getProductMeta( $product_id, self::META_CAPTION_BG, '#ffffff' ),
				'caption_title_color'   => $this->getProductMeta( $product_id, self::META_CAPTION_TITLE_COLOR, '#1e293b' ),
				'caption_text_color'    => $this->getProductMeta( $product_id, self::META_CAPTION_TEXT_COLOR, '#64748b' ),
				'caption_alignment'     => $this->getProductMeta( $product_id, self::META_CAPTION_ALIGNMENT, 'center' ),
				'caption_title_size'    => $this->getProductMeta( $product_id, self::META_CAPTION_TITLE_SIZE, 22 ),
				'caption_text_size'     => $this->getProductMeta( $product_id, self::META_CAPTION_TEXT_SIZE, 15 ),
				'caption_padding'       => $this->getProductMeta( $product_id, self::META_CAPTION_PADDING, 16 ),
				'caption_padding_x'     => $this->getCaptionPaddingAxis( $product_id, self::META_CAPTION_PADDING_X, 5 ),
				'caption_padding_y'     => $this->getCaptionPaddingAxis( $product_id, self::META_CAPTION_PADDING_Y, 9 ),
				'container_shadow'              => $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW, 0 ),
				'container_shadow_color'        => $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW_COLOR, '#000000' ),
				'container_shadow_horizontal'   => $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW_HORIZONTAL, 0 ),
				'container_shadow_vertical'     => $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW_VERTICAL, 8 ),
				'container_shadow_blur'         => $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW_BLUR, 20 ),
				'container_shadow_spread'       => $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW_SPREAD, 0 ),
				'container_shadow_opacity'      => $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW_OPACITY, 35 ),
				'container_border_width'        => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_WIDTH, 0 ),
				'container_border'              => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER, 0 ),
				'container_rounded'             => $this->getProductMeta( $product_id, self::META_CONTAINER_ROUNDED, 0 ),
				'container_border_color'        => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_COLOR, '#000000' ),
				'container_border_style'        => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_STYLE, 'solid' ),
				'container_border_radius_tl'    => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_RADIUS_TL, 0 ),
				'container_border_radius_tr'    => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_RADIUS_TR, 0 ),
				'container_border_radius_br'    => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_RADIUS_BR, 0 ),
				'container_border_radius_bl'    => $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_RADIUS_BL, 0 ),
			)
		);

		$placement = $this->getPlacement();
		$content   = $this->wrapDescription( $product_id, $slider );

		// Card frame: when the slider itself has no visible boundary (no
		// container border, no shadow), its edges melt into the product
		// sheet (white on white). The outer card then draws a hairline
		// frame so the comparator always reads as a distinct block,
		// whatever the theme background. Skipped when the slider already
		// draws its own border/shadow (avoids a double frame).
		$border_width_setting = (int) $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER_WIDTH, 0 );
		$has_frame            = $border_width_setting > 0
			|| '1' === (string) $this->getProductMeta( $product_id, self::META_CONTAINER_BORDER, 0 )
			|| '1' === (string) $this->getProductMeta( $product_id, self::META_CONTAINER_SHADOW, 0 );
		$card_class           = $has_frame ? '' : ' inar-pixvisualize-woo--framed';

		// Unique inar- wrapper (theme-proof): clears the summary floats/flex,
		// forces a full-width block below the summary and scopes our CSS so
		// no theme rule can leak in. Legacy pixvisualize-* classes are kept
		// for backward compatibility with custom CSS.
		echo '<div class="inar-pixvisualize-woo inar-pixvisualize-woo-placement--'
			. esc_attr( $placement ) . esc_attr( $card_class )
			. ' pixvisualize-woo-placement pixvisualize-woo-placement--'
			. esc_attr( $placement ) . '">'
			. $content // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal markup: buildSliderMarkup escapes every attribute, wrapDescription() applies wp_kses_post() to the description.
			. '</div>';
	}

	/**
	 * Wrap the slider markup with the optional product description according
	 * to the chosen content layout (top / bottom / left / right). Applies to
	 * every placement. Returns the slider untouched when no description set.
	 *
	 * @param  int    $product_id Product post ID.
	 * @param  string $slider     Built slider markup.
	 * @return string
	 */
	private function wrapDescription( $product_id, $slider )
	{
		$desc = get_post_meta( $product_id, self::META_DESC, true );

		if ( '' === trim( (string) $desc ) ) {
			return $slider;
		}

		$layout = get_post_meta( $product_id, self::META_DESC_LAYOUT, true );
		$layout = in_array( $layout, self::DESC_LAYOUTS, true ) ? $layout : 'top';

		// Text placement inside its own container: vertical position for
		// the 2-column layouts (class, applied via flexbox on desktop).
		// Horizontal alignment is handled by the editor toolbar itself
		// (TinyMCE writes text-align inline styles, preserved by kses).
		$desc_valign = get_post_meta( $product_id, self::META_DESC_VALIGN, true );
		$desc_valign = in_array( $desc_valign, self::DESC_VALIGNS, true ) ? $desc_valign : 'middle';

		$desc_html = '<div class="inar-pixvisualize-woo-desc pixvisualize-woo-desc'
			. ' inar-pixvisualize-woo-desc--valign-' . esc_attr( $desc_valign ) . '">'
			. wp_kses_post( $desc ) . '</div>';
		$wrapper   = '<div class="inar-pixvisualize-woo-desc-layout inar-pixvisualize-woo-desc-layout--'
			. esc_attr( $layout )
			. ' pixvisualize-woo-desc-layout pixvisualize-woo-desc-layout--'
			. esc_attr( $layout ) . '">';

		switch ( $layout ) {
			case 'bottom':
				return $wrapper . $slider . $desc_html . '</div>';
			case 'left':
				return $wrapper . $desc_html . $slider . '</div>';
			case 'right':
				return $wrapper . $slider . $desc_html . '</div>';
			case 'top':
			default:
				return $wrapper . $desc_html . $slider . '</div>';
		}
	}

	/**
	 * Read a PixVisualize product setting with fallback default.
	 *
	 * @param  int    $product_id Product post ID.
	 * @param  string $meta_key   Post meta key.
	 * @param  mixed  $default    Fallback value when the meta is empty.
	 * @return mixed
	 */
	private function getProductMeta( $product_id, $meta_key, $default )
	{
		$value = get_post_meta( $product_id, $meta_key, true );

		return '' === $value || false === $value ? $default : $value;
	}

	/**
	 * Read a caption padding axis (X/Y) with legacy fallback.
	 *
	 * Products saved before the X/Y split only have META_CAPTION_PADDING:
	 * reuse it for both axes so their caption height/spacing is preserved.
	 * Brand-new products (no legacy meta either) get the current defaults
	 * (X = 5, Y = 9).
	 */
	private function getCaptionPaddingAxis( $product_id, $meta_key, $default )
	{
		$value = get_post_meta( $product_id, $meta_key, true );

		if ( '' !== $value && false !== $value ) {
			return $value;
		}

		$legacy = get_post_meta( $product_id, self::META_CAPTION_PADDING, true );

		if ( '' !== $legacy && false !== $legacy ) {
			return $legacy;
		}

		return $default;
	}
}