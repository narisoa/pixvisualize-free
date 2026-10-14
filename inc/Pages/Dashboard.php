<?php
/**
 * Admin settings screen (PixVisualize → Settings).
 * License/Account/Pricing are native Freemius submenus (no custom License page).
 *
 * @package PixVisualize
 */
namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\ManagerCallbacks;

class Dashboard extends BaseController
{
	public $settings;

	public $callbacks;

	public $callbacks_mngr;

	public $pages = array();

	public function register()
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->callbacks_mngr = new ManagerCallbacks();

		$this->setPages();

		$this->setSettings();
		$this->setSections();
		$this->setFields();

		$this->settings->addPages( $this->pages )->withSubPage( __( 'Dashboard', 'pixvisualize' ) )->register();
	}

	public function setPages()
	{
		$this->pages = array(
			array(
				'page_title' => __( 'PixVisualize', 'pixvisualize' ),
				'menu_title' => __( 'PixVisualize', 'pixvisualize' ),
				'capability' => 'manage_options',
				'menu_slug' => 'pixvisualize',
				'callback' => array( $this->callbacks, 'adminDashboard' ),
				'icon_url' => $this->plugin_url . 'assets/images/admin-icon.svg',
				'position' => 110
			)
		);
	}

	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'pixvisualize_settings',
				'option_name' => 'pixvisualize',
				'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' )
			)
		);

		$this->settings->setSettings( $args );
	}

	public function setSections()
	{
		$args = array(
			array(
				'id' => 'pixvisualize_index',
				'title' => __( 'Settings Manager', 'pixvisualize' ),
				'callback' => array( $this->callbacks_mngr, 'adminSectionManager' ),
				'page' => 'pixvisualize'
			)
		);

		$this->settings->setSections( $args );
	}

	public function setFields()
	{
		$args = array();

		// WordPress.org compliant: no locked rows. Pro toggles appear only when usable.
		$show_pro_toggles = function_exists( 'pixvisualize_can_use_pro' ) && pixvisualize_can_use_pro();

		$descs = array(
			'pixvisualize_block_manager' => __( 'Slider in the block editor.', 'pixvisualize' ),
			'pixvisualize_elementor_manager' => __( 'Drag-and-drop widget with live preview.', 'pixvisualize' ),
			'pixvisualize_shortcode_manager' => __( 'Embed anywhere, plus the visual generator.', 'pixvisualize' ),
			'pixvisualize_woocommerce_manager' => __( 'Gallery on single product pages.', 'pixvisualize' ),
		);

		foreach ( $this->managers as $key => $value ) {
			if ( ! $show_pro_toggles && in_array( $key, array( 'pixvisualize_shortcode_manager', 'pixvisualize_woocommerce_manager' ), true ) ) {
				continue;
			}

			$title = $value;
			if ( isset( $descs[ $key ] ) ) {
				$title .= '<span class="pixvisualize-toggle-desc">' . $descs[ $key ] . '</span>';
			}

			$args[] = array(
				'id' => $key,
				'title' => $title,
				'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
				'page' => 'pixvisualize',
				'section' => 'pixvisualize_index',
				'args' => array(
					'option_name' => 'pixvisualize',
					'label_for' => $key,
					'class' => 'ui-toggle'
				)
			);
		}

		$this->settings->setFields( $args );
	}
}