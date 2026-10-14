<?php
/**
 * @package  PixVisualize
 */
namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * Registers the PixVisualize Elementor widget.
 *
 * The widget is only registered when Elementor is loaded, so the plugin
 * stays fully functional without Elementor (Gutenberg block keeps working).
 */
class ElementorController extends BaseController
{
	public function register()
	{
		// Optional gate: only register the widget when the Elementor manager
		// is activated on the PixVisualize settings page.
		if ( ! $this->activated( 'pixvisualize_elementor_manager' ) ) {
			return;
		}

		// Elementor may already be loaded (it fires 'elementor/loaded' when its
		// main file runs) or load after this plugin. Register the widget hooks
		// either way so the widget is always available on the front-end.
		if ( did_action( 'elementor/loaded' ) ) {
			$this->registerElementorHooks();
		} else {
			add_action( 'elementor/loaded', array( $this, 'registerElementorHooks' ) );
		}
	}

	public function registerElementorHooks()
	{
		add_action( 'elementor/widgets/register', array( $this, 'registerWidget' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueueEditorAssets' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueuePreviewAssets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'registerFrontendAssets' ) );
	}

	public function registerWidget( $widgets_manager )
	{
		$widgets_manager->register( new \Inc\Elementor\PixVisualizeWidget() );
	}

	public function enqueueEditorAssets()
	{
		wp_enqueue_script(
			'pixvisualize-elementor-editor',
			$this->plugin_url . 'assets/js/pixvisualize-elementor-editor.js',
			array( 'jquery' ),
			'1.0.6',
			true
		);

		wp_enqueue_style(
			'pixvisualize-elementor-editor',
			$this->plugin_url . 'assets/css/pixvisualize-elementor-editor.css',
			array(),
			'1.0.6'
		);

		$plan = function_exists( 'pixvisualize_get_plan' ) ? pixvisualize_get_plan() : 'free';
		$needs_install = function_exists( 'pixvisualize_needs_premium_install' ) && pixvisualize_needs_premium_install();
		wp_add_inline_script(
			'pixvisualize-elementor-editor',
			'window.pixvisualizePlan="' . esc_js( $plan ) . '";' .
			'window.pixvisualizeNeedsInstall=' . ( $needs_install ? 'true' : 'false' ) . ';',
			'before'
		);
	}

	/**
	 * Enqueue the preview script inside the Elementor editor preview iframe.
	 * It adds the live interaction behaviours (Move on Hover, Auto-Slide and
	 * Magnifier) on top of the static preview markup rendered by
	 * content_template().
	 */
	public function enqueuePreviewAssets()
	{
		wp_enqueue_script(
			'pixvisualize-elementor-preview',
			$this->plugin_url . 'assets/js/pixvisualize-elementor-preview.js',
			array(),
			'1.0.5',
			true
		);
	}

	/**
	 * Register the PixVisualize front-end view script and style so the
	 * Elementor widget can load them via get_script_depends()/get_style_depends().
	 *
	 * They are skipped when the Gutenberg block is already registered (the
	 * block registers the exact same handles), which avoids double-loading.
	 */
	public function registerFrontendAssets()
	{
		if ( wp_script_is( 'create-block-pixvisualize-view', 'registered' ) ) {
			return;
		}

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
}