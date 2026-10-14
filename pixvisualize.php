<?php
/**
 * PixVisualize - Before After Image Comparison Slider.
 *
 * Premium image comparison suite for WordPress: Gutenberg block,
 * Elementor widget, shortcode and WooCommerce gallery.
 * Free on WordPress.org, Pro via Freemius (pixvisualize-pro).
 *
 * @package PixVisualize
 */
/*
Plugin Name: PixVisualize - Before After Image Comparison Slider
Plugin URI: https://i-naridigit.com/products/pixvisualize/
Description: Before/After image comparison slider for Gutenberg, Elementor, shortcodes and WooCommerce. Fast, responsive, Pro-ready.
Version: 1.0.14
Requires at least: 6.8
Requires PHP: 7.4
Author: i-NariDigit
Author URI: https://i-naridigit.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: pixvisualize
Domain Path: /languages
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// If this file is called directly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

// Freemius bootstrap (Free + Pro). Handles Free/Pro coexistence:
// if the other version is already loaded, register this file and stop here
// to avoid a fatal redeclaration on activation.
if ( function_exists( 'pixvisualize_fs' ) ) {
	pixvisualize_fs()->set_basename( true, __FILE__ );
} else {
	/**
	 * DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE
	 * `function_exists` CALL ABOVE TO PROPERLY WORK.
	 */
	if ( ! function_exists( 'pixvisualize_fs' ) ) {
		require_once dirname( __FILE__ ) . '/inc/FreemiusIntegration.php';
	}

	if ( function_exists( 'pixvisualize_fs' ) ) {
		pixvisualize_fs()->set_basename( false, __FILE__ );
	}

/**
 * Plugin activation. Default settings are created once.
 */
function pixvisualize_activate() {
	Inc\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'pixvisualize_activate' );

/**
 * Plugin deactivation.
 */
function pixvisualize_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pixvisualize_deactivate' );

// Composer autoloader with built-in fallback.
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
} else {
	// Fallback PSR-4 autoloader (no Composer required)
	spl_autoload_register(
		function ( $class ) {
			$prefix   = 'Inc\\';
			$base_dir = dirname( __FILE__ ) . '/inc/';

			if ( strpos( $class, $prefix ) !== 0 ) {
				return;
			}

			$relative_class = substr( $class, strlen( $prefix ) );
			$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	);
}

/**
 * Load plugin translations.
 */
function pixvisualize_load_textdomain() {
	load_plugin_textdomain( 'pixvisualize', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'pixvisualize_load_textdomain' );

/**
 * Variant fallback: use the base language file when the exact locale file
 * is missing (fr_BE/fr_CH/... -> fr_FR, de_AT/de_CH/... -> de_DE,
 * es_MX/... -> es_ES, it_CH/... -> it_IT, pt_AO/... -> pt_PT).
 * A shipped variant file always wins (e.g. pixvisualize-fr_CA.mo).
 * English variants need nothing: the English msgids are the automatic
 * fallback when no file exists.
 *
 * @param string $locale Locale like 'fr_BE'.
 * @return string Base locale like 'fr_FR'.
 */
function pixvisualize_variant_base( $locale ) {
	if ( 0 === strpos( $locale, 'pt_' ) && 'pt_BR' !== $locale ) {
		return 'pt_PT';
	}
	$map = array( 'fr' => 'fr_FR', 'de' => 'de_DE', 'es' => 'es_ES', 'it' => 'it_IT' );
	$lang = substr( $locale, 0, 2 );
	if ( isset( $map[ $lang ] ) && $map[ $lang ] !== $locale ) {
		return $map[ $lang ];
	}
	return $locale;
}

/**
 * Redirect missing .mo files to the base language file (PHP strings).
 */
function pixvisualize_mofile_fallback( $mofile, $domain ) {
	if ( 'pixvisualize' !== $domain || ! is_string( $mofile ) || file_exists( $mofile ) ) {
		return $mofile;
	}
	if ( preg_match( '/pixvisualize-([a-zA-Z_]+)\.mo$/', $mofile, $matches ) ) {
		$base = pixvisualize_variant_base( $matches[1] );
		$candidate = preg_replace( '/pixvisualize-[a-zA-Z_]+\.mo$/', 'pixvisualize-' . $base . '.mo', $mofile );
		if ( $base !== $matches[1] && file_exists( $candidate ) ) {
			return $candidate;
		}
	}
	return $mofile;
}
add_filter( 'load_textdomain_mofile', 'pixvisualize_mofile_fallback', 10, 2 );

/**
 * Redirect missing script translation files to the base language file
 * (JS strings loaded via wp_set_script_translations).
 */
function pixvisualize_script_fallback( $file, $handle, $domain ) {
	if ( 'pixvisualize' !== $domain || ! is_string( $file ) || '' === $file || file_exists( $file ) ) {
		return $file;
	}
	if ( preg_match( '/pixvisualize-([a-zA-Z_]+)-(.+)\.json$/', $file, $matches ) ) {
		$base = pixvisualize_variant_base( $matches[1] );
		$candidate = str_replace( 'pixvisualize-' . $matches[1] . '-', 'pixvisualize-' . $base . '-', $file );
		if ( $base !== $matches[1] && file_exists( $candidate ) ) {
			return $candidate;
		}
	}
	return $file;
}
add_filter( 'load_script_translation_file', 'pixvisualize_script_fallback', 10, 3 );

	/**
	 * Boot plugin services.
	 */
	if ( class_exists( 'Inc\\Init' ) ) {
		Inc\Init::registerServices();
	}
}
