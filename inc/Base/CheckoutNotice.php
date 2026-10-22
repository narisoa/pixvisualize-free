<?php
/**
 * @package PixVisualize
 */

namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * Post-checkout safety net (customer point of view).
 *
 * Freemius only shows its own "purchase completed, check your email"
 * sticky notice when the site is registered (SDK: `is_registered() &&
 * fs_request_has( 'purchase_completed' )`). A customer who bought while
 * anonymous lands back on `?page=pixvisualize` with zero guidance.
 *
 * This notice fills that gap: shown on our own screens, only when the
 * checkout return params are present and Pro is NOT active yet. No custom
 * key handling — it points to the native activation dialog.
 */
class CheckoutNotice extends BaseController
{
	public function register()
	{
		add_action( 'admin_notices', array( $this, 'maybeShowNotice' ) );
	}

	public function maybeShowNotice()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Read-only admin notices: these query args only drive display.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended

		// Cas piège UX : licencié mais toujours sur le code Free.
		// Les contrôles Pro restent cachés (sinon inertes en front) :
		// on force l'installation du build premium, sur nos écrans
		// ET sur Extensions (où se fait l'upload).
		$needs_install = function_exists( 'pixvisualize_needs_premium_install' ) && pixvisualize_needs_premium_install();
		$can_use_pro = function_exists( 'pixvisualize_can_use_pro' ) && pixvisualize_can_use_pro();

		if ( $needs_install ) {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			$on_plugins = isset( $_GET['page'] ) ? false : ( $screen && 'plugins' === $screen->id );
		$on_ours = isset( $_GET['page'] ) && 'pixvisualize' === sanitize_key( wp_unslash( $_GET['page'] ) );
		if ( ! $on_plugins && ! $on_ours ) {
			return;
		}
		$upload_url = esc_url( admin_url( 'plugin-install.php?tab=upload' ) );
		$account_url = ( function_exists( 'pixvisualize_fs' ) && is_callable( array( pixvisualize_fs(), 'get_account_url' ) ) )
			? esc_url( pixvisualize_fs()->get_account_url() )
			: esc_url( admin_url( 'plugins.php' ) );
			?>
			<div class="notice notice-warning is-dismissible">
				<p><strong><?php esc_html_e( 'Pro license active — install the Pro version to unlock everything.', 'pixvisualize' ); ?></strong>
				<?php esc_html_e( 'Your license is active on the Free code: upload pixvisualize-pro.zip (purchase email or Account) via Extensions → Add → Upload, then activate it.', 'pixvisualize' ); ?></p>
				<p><a class="button button-primary" href="<?php echo esc_url( $upload_url ); ?>"><?php esc_html_e( 'Upload the Pro version', 'pixvisualize' ); ?></a>
				<a class="button" style="margin-left:4px;" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'Open my Account', 'pixvisualize' ); ?></a></p>
			</div>
			<?php
			return;
		}

		// Pro utilisable : rien à expliquer.
		if ( $can_use_pro ) {
			return;
		}

		if ( ! isset( $_GET['page'] ) ) {
			return;
		}

		$page = sanitize_key( wp_unslash( $_GET['page'] ) );
		if ( 'pixvisualize' !== $page ) {
			return;
		}

		// Freemius appends these on checkout return (?purchase_completed=...).
		if ( ! isset( $_GET['purchase_completed'] ) ) {
			return;
		}

		$plan = isset( $_GET['purchased_plan'] )
			? sanitize_text_field( wp_unslash( $_GET['purchased_plan'] ) )
			: '';
		$email = isset( $_GET['purchase_email'] )
			? sanitize_email( wp_unslash( $_GET['purchase_email'] ) )
			: '';

		$plugins_url = esc_url( admin_url( 'plugins.php' ) );
		$account_url = ( function_exists( 'pixvisualize_fs' ) && is_callable( array( pixvisualize_fs(), 'get_account_url' ) ) && pixvisualize_fs()->is_registered() )
			? esc_url( pixvisualize_fs()->get_account_url() )
			: '';

		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<strong><?php esc_html_e( 'Thank you for your purchase!', 'pixvisualize' ); ?></strong>
				<?php
				if ( '' !== $plan ) {
					echo ' ' . sprintf(
						/* translators: %s: purchased plan name. */
						esc_html__( 'Your %s license key and installation instructions were sent by email.', 'pixvisualize' ),
						'<strong>' . esc_html( $plan ) . '</strong>'
					);
				} else {
					echo ' ' . esc_html__( 'Your license key and installation instructions were sent by email.', 'pixvisualize' );
				}
				if ( '' !== $email ) {
					echo ' ' . sprintf(
						/* translators: %s: buyer email address. */
						esc_html__( 'Check the inbox of %s (and the spam folder after 5 min).', 'pixvisualize' ),
						'<strong>' . esc_html( $email ) . '</strong>'
					);
				}
				?>
			</p>
			<p>
				<?php esc_html_e( 'To activate Pro on this site:', 'pixvisualize' ); ?>
				<?php esc_html_e( '1) Open Extensions, 2) click "Activate License" on the PixVisualize row, 3) paste the key and confirm.', 'pixvisualize' ); ?>
				<a class="button button-primary" style="margin-left:8px;" href="<?php echo esc_url( $plugins_url ); ?>"><?php esc_html_e( 'Open Extensions', 'pixvisualize' ); ?></a>
				<?php if ( '' !== $account_url ) : ?>
					<a class="button" style="margin-left:4px;" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'Open my Account', 'pixvisualize' ); ?></a>
				<?php endif; ?>
			</p>
		</div>
		<?php
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
