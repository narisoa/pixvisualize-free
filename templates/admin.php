<?php

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1><img src="<?php 
echo esc_url( $this->plugin_url . 'assets/images/pixvisualize-logo.webp' );
?>" alt="<?php 
esc_attr_e( 'PixVisualize', 'pixvisualize' );
?>" width="28" height="28" style="vertical-align:-6px;margin-right:8px;"><?php 
esc_html_e( 'PixVisualize', 'pixvisualize' );
?></h1>
	<?php 
settings_errors();
?>

	<?php 
$is_pro = function_exists( 'pixvisualize_is_pro' ) && pixvisualize_is_pro();
$can_use_pro = function_exists( 'pixvisualize_can_use_pro' ) && pixvisualize_can_use_pro();
$needs_install = function_exists( 'pixvisualize_needs_premium_install' ) && pixvisualize_needs_premium_install();
$show_generator = $can_use_pro && $this->activated( 'pixvisualize_shortcode_manager' );
$about_num = ( $show_generator ? 3 : 2 );
$gopro_num = ( $show_generator ? 4 : 3 );
// Inline HTML below is ours (strong/code only): translators must not
// add other tags, wp_kses() enforces it on output.
$pixvisualize_allowed_inline = array(
    'strong' => array(),
    'code'   => array(),
);
?>

	<?php 
if ( $needs_install ) {
    ?>
		<div class="notice notice-warning pixvisualize-install-pro" style="margin:12px 0;padding:12px 16px;border-left:4px solid #dba617;">
			<p><strong><?php 
    esc_html_e( 'Pro license active — install the Pro version to unlock everything.', 'pixvisualize' );
    ?></strong></p>
			<p><?php 
    esc_html_e( 'Your license is active on the Free code: the Pro controls stay hidden until pixvisualize-pro is installed (otherwise they would look working in the editor but do nothing on the site).', 'pixvisualize' );
    ?></p>
			<p><?php 
    esc_html_e( '1) Download pixvisualize-pro.zip from your purchase email or your Freemius Account, 2) go to Extensions → Add → Upload, 3) activate — the Free version deactivates itself.', 'pixvisualize' );
    ?>
				<a class="button button-primary" style="margin-left:8px;" href="<?php 
    echo esc_url( admin_url( 'plugin-install.php?tab=upload' ) );
    ?>"><?php 
    esc_html_e( 'Upload the Pro version', 'pixvisualize' );
    ?></a>
				<?php 
    if ( function_exists( 'pixvisualize_fs' ) && is_callable( array(pixvisualize_fs(), 'get_account_url') ) ) {
        ?>
					<a class="button" style="margin-left:4px;" href="<?php 
        echo esc_url( pixvisualize_fs()->get_account_url() );
        ?>"><?php 
        esc_html_e( 'Open my Account', 'pixvisualize' );
        ?></a>
				<?php 
    }
    ?>
			</p>
		</div>
	<?php 
}
?>

	<?php 
if ( !$is_pro ) {
    ?>
		<div class="pixvisualize-banner">
			<div class="pixvisualize-banner__text">
				<strong><?php 
    esc_html_e( 'PixVisualize Pro', 'pixvisualize' );
    ?></strong> — <?php 
    esc_html_e( 'unlock all the advanced features: native Elementor widget, shortcodes, WooCommerce, magnifier, auto-scan, responsive…', 'pixvisualize' );
    ?>
			</div>
			<a class="button button-primary" href="<?php 
    echo esc_url( ( function_exists( 'pixvisualize_fs' ) ? pixvisualize_fs()->get_upgrade_url() : '#' ) );
    ?>"><?php 
    esc_html_e( 'Go Pro', 'pixvisualize' );
    ?></a>
			<a class="button button-secondary" style="margin-left:8px;" href="<?php 
    echo esc_url( ( function_exists( 'pixvisualize_get_demo_url' ) ? pixvisualize_get_demo_url() : 'https://i-naridigit.com/demo/pixvisualize/' ) );
    ?>" target="_blank" rel="noopener"><?php 
    esc_html_e( 'View Pro demo', 'pixvisualize' );
    ?></a>
		</div>
	<?php 
}
?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1"><span class="dashicons dashicons-admin-settings" aria-hidden="true"></span><?php 
esc_html_e( 'Manage Settings', 'pixvisualize' );
?></a></li>
		<?php 
?>
		<li><a href="#tab-<?php 
echo esc_html( $about_num );
?>"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span><?php 
esc_html_e( 'About', 'pixvisualize' );
?></a></li>
		<?php 
if ( !$is_pro ) {
    ?>
			<li><a href="#tab-<?php 
    echo esc_html( $gopro_num );
    ?>"><span class="dashicons dashicons-star-filled" aria-hidden="true"></span><?php 
    esc_html_e( 'Go Pro', 'pixvisualize' );
    ?></a></li>
		<?php 
}
?>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<div class="pc-pane">
				<h2><?php 
esc_html_e( 'Manage Settings', 'pixvisualize' );
?></h2>
				<p class="pc-pane__intro">
					<?php 
esc_html_e( 'Enable or disable each PixVisualize feature globally. Block settings (images, labels, position, ratio) are saved by Gutenberg in your post content and are never affected by this page.', 'pixvisualize' );
?>
				</p>

				<form method="post" action="options.php" class="pixvisualize-settings-form">
					<?php 
settings_fields( 'pixvisualize_settings' );
do_settings_sections( 'pixvisualize' );
submit_button();
?>
				</form>
			</div>
		</div>

		<?php 
?>

		<div id="tab-<?php 
echo esc_html( $about_num );
?>" class="tab-pane">
			<div class="pc-pane">
				<h2><?php 
esc_html_e( 'About', 'pixvisualize' );
?></h2>

				<?php 
if ( $is_pro ) {
    ?>
					<p>
						<?php 
    echo wp_kses( __( '<strong>PixVisualize</strong> is the complete Before/After image comparison suite for WordPress. Compare two images with an interactive sliding cursor through a native Gutenberg block, a dedicated Elementor widget, shortcodes and full WooCommerce integration.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?>
					</p>
				<?php 
} else {
    ?>
					<p>
						<strong><?php 
    esc_html_e( 'PixVisualize', 'pixvisualize' );
    ?></strong> <?php 
    esc_html_e( 'provides an interactive Gutenberg block to compare two images (Before/After) with an overlaid sliding cursor.', 'pixvisualize' );
    ?>
					</p>
					<p>
						<?php 
    esc_html_e( 'Built natively with', 'pixvisualize' );
    ?> <strong><?php 
    esc_html_e( 'React', 'pixvisualize' );
    ?></strong> <?php 
    esc_html_e( 'and the @wordpress/scripts ecosystem, the block works both in the editor (back-office) and on the public site (front-office).', 'pixvisualize' );
    ?>
					</p>
					<p>
						<?php 
    esc_html_e( 'The settings of each block (images, labels, position, ratio) are saved by Gutenberg in the content of your posts and pages: they are never affected by this settings page.', 'pixvisualize' );
    ?>
					</p>
				<?php 
}
?>

				<?php 
if ( $is_pro ) {
    ?>
				<h3><?php 
    esc_html_e( 'Pro Features', 'pixvisualize' );
    ?></h3>
				<div class="pc-pro-features">
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-layout" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Advanced Elementor widget</strong> with visual controls and a live WYSIWYG preview.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-admin-customizer" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Custom handle styles</strong>: circle, transparent, line, double arrows or your own SVG icon, with independent size, arrows color and padding.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-tag" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Advanced labels</strong>: position, typography, colors and margins.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-art" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Cursor & handle</strong>: one synchronized color for the bar and the circle, plus a grouped drop shadow with no seam.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-image-flip-vertical" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Vertical orientation</strong> (Top/Bottom) in addition to horizontal.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-smartphone" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Responsive per breakpoint</strong>: tablet/mobile position and ratio.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-move" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Hover Mode</strong>: the cursor follows the pointer.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-controls-play" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Auto-Scan</strong> intro animation.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-search" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Magnifier Glass</strong> zoom effect.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-editor-code" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Shortcode generator</strong>: build a <code>[pixvisualize]</code> shortcode visually, keep a saved list and reload any shortcode for editing.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-cart" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>WooCommerce support</strong>: Before/After gallery on single product pages.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-editor-paragraph" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Custom captions</strong>: title + text with background, colors, alignment, sizes and padding.', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-admin-appearance" aria-hidden="true"></span>
						<p><?php 
    echo wp_kses( __( '<strong>Container style</strong>: custom shadow, border and per-corner radius (free ON/OFF toggles included).', 'pixvisualize' ), $pixvisualize_allowed_inline );
    ?></p>
					</div>
				</div>

				<h3><?php 
    esc_html_e( 'Settings Manager', 'pixvisualize' );
    ?></h3>
				<p>
					<?php 
    esc_html_e( 'The toggles above activate or deactivate each feature globally. Block settings (images, labels, position, ratio) are saved by Gutenberg in your post content and are never affected by this page. When an option is missing (fresh install), every feature stays active by default for full backward compatibility.', 'pixvisualize' );
    ?>
				</p>
				<?php 
}
?>
			</div>
		</div>

		<?php 
if ( !$is_pro ) {
    ?>
		<div id="tab-<?php 
    echo esc_html( $gopro_num );
    ?>" class="tab-pane">
			<div class="pc-pane">
				<h2><?php 
    esc_html_e( 'Go Pro', 'pixvisualize' );
    ?></h2>
				<p class="pc-pane__intro">
					<?php 
    esc_html_e( 'PixVisualize Pro extends the Free block with advanced controls, additional rendering modes and dedicated integrations — all with the same intuitive Gutenberg experience.', 'pixvisualize' );
    ?>
				</p>

				<table class="pc-table">
					<thead>
						<tr>
							<th><?php 
    esc_html_e( 'Feature', 'pixvisualize' );
    ?></th>
							<th class="pc-table__free-col"><?php 
    esc_html_e( 'Free', 'pixvisualize' );
    ?></th>
							<th class="pc-table__pro-col"><?php 
    esc_html_e( 'Pro', 'pixvisualize' );
    ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Comparateur Avant / Après', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Libellés Avant / Après personnalisables', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Affichage / masquage des libellés', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Orientation horizontale', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Orientation verticale', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Format et hauteur personnalisables', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Position initiale du curseur', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Pleine largeur et alignement', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Légende sous le comparateur', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Personnalisation complète de la légende', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Design responsive avancé', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Personnalisation de la poignée et du séparateur', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Libellés entièrement personnalisables', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Bordures et coins personnalisables', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Ombre personnalisable', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Déplacement au survol', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Balayage automatique', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Loupe avec zoom', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Shortcodes sauvegardés', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Modification visuelle des shortcodes existants', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Bloc Gutenberg', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Réglages avancés Gutenberg', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Widget Elementor', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Style et prévisualisation WYSIWYG Elementor', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php 
    esc_html_e( 'Galerie Avant / Après WooCommerce', 'pixvisualize' );
    ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php 
    esc_html_e( 'No', 'pixvisualize' );
    ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php 
    esc_html_e( 'Yes', 'pixvisualize' );
    ?></span></td>
						</tr>
					</tbody>
				</table>

				<h3 class="pc-pane__section-title"><?php 
    esc_html_e( 'Why upgrade?', 'pixvisualize' );
    ?></h3>
				<div class="pc-pro-features">
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-clock"></span>
						<strong><?php 
    esc_html_e( 'Save hours', 'pixvisualize' );
    ?></strong>
						<p><?php 
    esc_html_e( 'Embed comparisons anywhere with shortcodes and the WooCommerce gallery — no block editor needed.', 'pixvisualize' );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-awards"></span>
						<strong><?php 
    esc_html_e( 'Impress clients', 'pixvisualize' );
    ?></strong>
						<p><?php 
    esc_html_e( 'Hover mode, magnifier glass and the Auto-Scan intro make every demo memorable.', 'pixvisualize' );
    ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-admin-customizer"></span>
						<strong><?php 
    esc_html_e( 'Full control', 'pixvisualize' );
    ?></strong>
						<p><?php 
    esc_html_e( 'A native Elementor widget with visual styling, vertical orientation and per-breakpoint responsive settings.', 'pixvisualize' );
    ?></p>
					</div>
				</div>

				<div class="pc-callouts">
					<div class="pc-callout pc-callout--migration">
						<strong><?php 
    esc_html_e( 'Zero-effort upgrade', 'pixvisualize' );
    ?></strong>
						<p><?php 
    esc_html_e( 'Same block name — your existing blocks keep working after switching to Pro, no re-editing.', 'pixvisualize' );
    ?></p>
					</div>
					<div class="pc-callout pc-callout--guarantee">
						<strong><?php 
    esc_html_e( 'Priority support included', 'pixvisualize' );
    ?></strong>
						<p><?php 
    esc_html_e( 'Dedicated support: get fast help to configure and integrate your comparisons without technical roadblocks.', 'pixvisualize' );
    ?></p>
					</div>
				</div>

				<div class="pc-pane__cta">
					<a class="button button-primary button-hero" href="<?php 
    echo esc_url( ( function_exists( 'pixvisualize_fs' ) ? pixvisualize_fs()->get_upgrade_url() : '#' ) );
    ?>"><?php 
    esc_html_e( 'Go Pro now', 'pixvisualize' );
    ?></a>
					<a class="button button-secondary button-hero" style="margin-left:8px;" href="<?php 
    echo esc_url( ( function_exists( 'pixvisualize_get_demo_url' ) ? pixvisualize_get_demo_url() : 'https://i-naridigit.com/demo/pixvisualize/' ) );
    ?>" target="_blank" rel="noopener"><?php 
    esc_html_e( 'View Pro demo', 'pixvisualize' );
    ?></a>
				</div>
			</div>
		</div>
		<?php 
}
?>
	</div>
</div>