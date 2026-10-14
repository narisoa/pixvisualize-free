<?php
/**
 * Activation defaults.
 *
 * All features are enabled by default for full backward compatibility.
 *
 * @package PixVisualize
 */
namespace Inc\Base;

class Activate
{
	public static function activate() {
		flush_rewrite_rules();

		$default = array(
			'pixvisualize_block_manager'       => true,
			'pixvisualize_elementor_manager'   => true,
			'pixvisualize_shortcode_manager'   => true,
			'pixvisualize_woocommerce_manager' => true,
		);

		if ( ! get_option( 'pixvisualize' ) ) {
			update_option( 'pixvisualize', $default );
		}

		set_transient( 'pixvisualize_welcome_notice', 1, 60 );
	}
}