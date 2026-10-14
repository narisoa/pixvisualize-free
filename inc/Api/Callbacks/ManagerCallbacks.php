<?php
/**
 * @package  PixVisualize
 */
namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class ManagerCallbacks extends BaseController
{
	public function checkboxSanitize( $input )
	{
		$output = array();
		$existing = get_option( 'pixvisualize', array() );

		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		// Lignes Pro non affichées sans code premium utilisable : on conserve
		// la valeur existante au lieu de l'écraser à false, pour ne pas
		// désactiver les features lors d'une installation Pro ultérieure.
		// Les cases affichées gardent la sémantique normale
		// (absent du POST = décoché = false).
		$pro_toggles_hidden = ! function_exists( 'pixvisualize_can_use_pro' ) || ! pixvisualize_can_use_pro();
		$pro_only = array( 'pixvisualize_shortcode_manager', 'pixvisualize_woocommerce_manager' );

		foreach ( $this->managers as $key => $value ) {
			if ( isset( $input[ $key ] ) ) {
				$output[ $key ] = true;
			} elseif ( $pro_toggles_hidden && in_array( $key, $pro_only, true ) && array_key_exists( $key, $existing ) ) {
				$output[ $key ] = (bool) $existing[ $key ];
			} else {
				$output[ $key ] = false;
			}
		}

		return $output;
	}

	public function adminSectionManager()
	{
		echo esc_html__( 'Manage the Sections and Features of this Plugin by activating the checkboxes from the following list.', 'pixvisualize' );
	}

	public function checkboxField( $args )
	{
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checkbox = get_option( $option_name );
		$checked = isset( $checkbox[$name] ) ? ( $checkbox[$name] ? true : false ) : true;

		echo '<div class="' . esc_attr( $classes ) . '"><input type="checkbox" id="' . esc_attr( $name ) . '" name="' . esc_attr( $option_name ) . '[' . esc_attr( $name ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '') . '><label for="' . esc_attr( $name ) . '"><div></div></label></div>';
	}
}