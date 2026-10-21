<?php

/**
 * @package  PixVisualize
 */
namespace Inc\Base;

use Inc\Base\BaseController;
/**
 * Enqueue admin dashboard assets
 */
class Enqueue extends BaseController {
    public function register() {
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue') );
    }

    function enqueue( $hook ) {
        // Only load the assets on the plugin settings + welcome pages
        if ( 'toplevel_page_pixvisualize' !== $hook && 'toplevel_page_pixvisualize_welcome' !== $hook ) {
            return;
        }
        // Version the assets by file mtime so the browser never serves a stale
        // cached stylesheet/script after an update.
        $style_version = ( file_exists( $this->plugin_path . 'assets/css/pixvisualize-admin.css' ) ? filemtime( $this->plugin_path . 'assets/css/pixvisualize-admin.css' ) : null );
        $tabs_version = ( file_exists( $this->plugin_path . 'assets/js/pixvisualize-admin-tabs.js' ) ? filemtime( $this->plugin_path . 'assets/js/pixvisualize-admin-tabs.js' ) : null );
        wp_enqueue_style(
            'pixvisualize-admin',
            $this->plugin_url . 'assets/css/pixvisualize-admin.css',
            array(),
            $style_version
        );
        wp_enqueue_script(
            'pixvisualize-admin-tabs',
            $this->plugin_url . 'assets/js/pixvisualize-admin-tabs.js',
            array(),
            $tabs_version,
            true
        );
    }

}
