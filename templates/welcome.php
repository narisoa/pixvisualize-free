<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
	<div class="pc-pane pc-welcome">
		<div class="pc-welcome__hero">
			<img class="pc-welcome__icon" src="<?php echo esc_url( $this->plugin_url . 'assets/images/pixvisualize-logo.webp' ); ?>" alt="PixVisualize" width="68" height="68">
			<div>
				<h1><?php esc_html_e( 'Welcome to PixVisualize!', 'pixvisualize' ); ?></h1>
				<p class="pc-welcome__tagline">
					<?php esc_html_e( 'A modern, ultra-fast Before/After image comparison slider for the Gutenberg block editor.', 'pixvisualize' ); ?>
				</p>
			</div>
		</div>

		<h2 class="pc-welcome__title"><?php esc_html_e( 'Get started in 3 steps', 'pixvisualize' ); ?></h2>

		<ol class="pc-welcome__steps">
			<li>
				<strong><?php esc_html_e( 'Add the block', 'pixvisualize' ); ?></strong>
				<span><?php esc_html_e( 'In the editor, search for "PixVisualize" in the block inserter and add it to your content.', 'pixvisualize' ); ?></span>
			</li>
			<li>
				<strong><?php esc_html_e( 'Pick your images', 'pixvisualize' ); ?></strong>
				<span><?php esc_html_e( 'Select a Before and an After image, customize the labels and the starting position.', 'pixvisualize' ); ?></span>
			</li>
			<li>
				<strong><?php esc_html_e( 'Publish & drag', 'pixvisualize' ); ?></strong>
				<span><?php esc_html_e( 'Your visitors can now slide the cursor with the mouse or a finger on mobile.', 'pixvisualize' ); ?></span>
			</li>
		</ol>

		<?php if ( ! function_exists( 'pixvisualize_is_pro' ) || ! pixvisualize_is_pro() ) : ?>
		<div class="pc-welcome__pro">
			<h2 class="pc-welcome__title"><?php esc_html_e( 'Take it further with PixVisualize Pro', 'pixvisualize' ); ?></h2>
			<ul class="pc-welcome__pro-list">
				<li><?php esc_html_e( 'Native Elementor widget with full controls & styling', 'pixvisualize' ); ?></li>
				<li><?php esc_html_e( 'Shortcode generation & WooCommerce product gallery', 'pixvisualize' ); ?></li>
				<li><?php esc_html_e( 'Hover mode, magnifier glass and Auto-Scan intro', 'pixvisualize' ); ?></li>
				<li><?php esc_html_e( 'Vertical orientation, responsive breakpoints & custom handles', 'pixvisualize' ); ?></li>
			</ul>
			<p class="pc-welcome__pro-note">
				<?php esc_html_e( 'Licences for 1, 3 or 5 sites, managed by Freemius. Upgrade anytime, your content stays intact.', 'pixvisualize' ); ?>
			</p>
			<a class="button button-primary button-hero" href="<?php echo esc_url( function_exists( 'pixvisualize_fs' ) ? pixvisualize_fs()->get_upgrade_url() : admin_url( 'admin.php?page=pixvisualize-pricing' ) ); ?>">
				<?php esc_html_e( 'Discover PixVisualize Pro', 'pixvisualize' ); ?>
			</a>
		</div>
		<?php endif; ?>

		<div class="pc-welcome__actions">
			<a class="button button-hero" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>">
				<?php esc_html_e( 'Start building', 'pixvisualize' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=pixvisualize' ) ); ?>">
				<?php esc_html_e( 'Open settings', 'pixvisualize' ); ?>
			</a>
		</div>
	</div>
</div>
