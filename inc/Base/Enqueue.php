<?php
/**
 * @package  PixVisualize
 */
namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * Enqueue admin dashboard assets
 */
class Enqueue extends BaseController
{
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	function enqueue( $hook ) {
		// Only load the assets on the plugin settings + welcome pages
		if ( 'toplevel_page_pixvisualize' !== $hook && 'toplevel_page_pixvisualize_welcome' !== $hook ) {
			return;
		}

		wp_enqueue_media();

		// Version the assets by file mtime so the browser never serves a stale
		// cached stylesheet/script after an update.
		$style_version = file_exists( $this->plugin_path . 'assets/css/pixvisualize-admin.css' ) ? filemtime( $this->plugin_path . 'assets/css/pixvisualize-admin.css' ) : null;
		$script_version = file_exists( $this->plugin_path . 'assets/js/pixvisualize-admin.js' ) ? filemtime( $this->plugin_path . 'assets/js/pixvisualize-admin.js' ) : null;

		wp_enqueue_style( 'pixvisualize-admin', $this->plugin_url . 'assets/css/pixvisualize-admin.css', array(), $style_version );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'pixvisualize-admin', $this->plugin_url . 'assets/js/pixvisualize-admin.js', array( 'jquery', 'wp-i18n', 'wp-color-picker' ), $script_version, true );
		wp_set_script_translations( 'pixvisualize-admin', 'pixvisualize', $this->plugin_path . 'languages/' );

		// Live preview interactivity (hover / auto-scan / magnifier) for the
		// shortcode generator, shared with the Elementor editor preview.
		$preview_version = file_exists( $this->plugin_path . 'assets/js/pixvisualize-elementor-preview.js' ) ? filemtime( $this->plugin_path . 'assets/js/pixvisualize-elementor-preview.js' ) : null;
		wp_enqueue_script( 'pixvisualize-generator-preview', $this->plugin_url . 'assets/js/pixvisualize-elementor-preview.js', array(), $preview_version, true );
	}
}