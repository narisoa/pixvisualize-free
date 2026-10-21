<?php
/**
 * @package  PixVisualize
 */
namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * One-time Welcome experience shown after activation.
 *
 * The Welcome screen is registered as a top-level page then immediately removed
 * from the menu, so it stays reachable at admin.php?page=pixvisualize_welcome
 * without cluttering the admin menu (a page removed with remove_menu_page()
 * remains directly accessible, unlike one removed with remove_submenu_page()).
 *
 * Instead of a forced redirect after activation (rejected by the WordPress.org
 * Plugin Review Team), a single dismissible notice is shown on the plugins
 * screen inviting the user to open the Welcome screen or the settings page.
 */
class Welcome extends BaseController
{
	const PAGE_SLUG = 'pixvisualize_welcome';

	const NOTICE_TRANSIENT = 'pixvisualize_welcome_notice';

	const NOTICE_DISMISS_KEY = 'pixvisualize_welcome_dismissed';

	const NOTICE_ACTION = 'pixvisualize_welcome_dismiss';

	public function register()
	{
		add_action( 'admin_menu', array( $this, 'addPage' ) );
		add_action( 'admin_notices', array( $this, 'maybeShowNotice' ) );
		add_action( 'wp_ajax_' . self::NOTICE_ACTION, array( $this, 'dismissNotice' ) );
		add_action( 'load-toplevel_page_' . self::PAGE_SLUG, array( $this, 'clearNotice' ) );
	}

	public function addPage()
	{
		add_menu_page(
			__( 'Welcome to PixVisualize', 'pixvisualize' ),
			__( 'Welcome', 'pixvisualize' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render' ),
			$this->plugin_url . 'assets/images/admin-icon.svg'
		);

		remove_menu_page( self::PAGE_SLUG );
	}

	public function maybeShowNotice()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Only on the plugins screen, right after a single activation.
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'plugins' !== $screen->id ) {
			return;
		}

		if ( ! get_transient( self::NOTICE_TRANSIENT ) ) {
			return;
		}

		if ( get_user_meta( get_current_user_id(), self::NOTICE_DISMISS_KEY, true ) ) {
			return;
		}

		$welcome_url  = esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) );
		$settings_url = esc_url( admin_url( 'admin.php?page=pixvisualize' ) );
		$ajax_url     = esc_url_raw( admin_url( 'admin-ajax.php' ) );
		$nonce        = wp_create_nonce( self::NOTICE_ACTION );

		?>
		<div id="pixvisualize-welcome-notice" class="notice notice-info is-dismissible">
			<p>
				<strong><?php esc_html_e( 'Welcome to PixVisualize!', 'pixvisualize' ); ?></strong>
				<?php esc_html_e( 'Get started in 3 steps or jump straight to the settings.', 'pixvisualize' ); ?>
				<a class="button button-primary" style="margin-left:8px;" href="<?php echo esc_url( $welcome_url ); ?>"><?php esc_html_e( 'Open the Welcome screen', 'pixvisualize' ); ?></a>
				<a class="button" style="margin-left:4px;" href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Settings', 'pixvisualize' ); ?></a>
			</p>
		</div>
		<?php

		wp_register_script( 'pixvisualize-welcome-notice', '', array(), '1.0.19', true );
		wp_enqueue_script( 'pixvisualize-welcome-notice' );
		wp_add_inline_script(
			'pixvisualize-welcome-notice',
			"(function(){var n=document.getElementById('pixvisualize-welcome-notice');if(!n){return;}var d=n.querySelector('.notice-dismiss');if(!d){return;}d.addEventListener('click',function(){var f=new FormData();f.append('action','" . esc_js( self::NOTICE_ACTION ) . "');f.append('nonce','" . esc_js( $nonce ) . "');fetch('" . esc_url( $ajax_url ) . "',{method:'POST',body:f});});})();"
		);
	}

	public function dismissNotice()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'pixvisualize' ) ), 403 );
		}

		check_ajax_referer( self::NOTICE_ACTION, 'nonce' );

		update_user_meta( get_current_user_id(), self::NOTICE_DISMISS_KEY, 1 );
		delete_transient( self::NOTICE_TRANSIENT );

		wp_send_json_success();
	}

	public function clearNotice()
	{
		// Once the user opens the Welcome screen, the notice has done its job.
		delete_transient( self::NOTICE_TRANSIENT );
	}

	public function render()
	{
		require_once $this->plugin_path . 'templates/welcome.php';
	}
}
