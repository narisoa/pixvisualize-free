<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
	<h1><img src="<?php echo esc_url( $this->plugin_url . 'assets/images/pixvisualize-logo.webp' ); ?>" alt="<?php esc_attr_e( 'PixVisualize', 'pixvisualize' ); ?>" width="28" height="28" style="vertical-align:-6px;margin-right:8px;"><?php esc_html_e( 'PixVisualize', 'pixvisualize' ); ?></h1>
	<?php settings_errors(); ?>

	<?php
	$is_pro = function_exists( 'pixvisualize_is_pro' ) && pixvisualize_is_pro();
	$can_use_pro = function_exists( 'pixvisualize_can_use_pro' ) && pixvisualize_can_use_pro();
	$needs_install = function_exists( 'pixvisualize_needs_premium_install' ) && pixvisualize_needs_premium_install();
	$show_generator = $can_use_pro && $this->activated( 'pixvisualize_shortcode_manager' );
	$about_num = $show_generator ? 3 : 2;
	$gopro_num = $show_generator ? 4 : 3;
	// Inline HTML below is ours (strong/code only): translators must not
	// add other tags, wp_kses() enforces it on output.
	$pixvisualize_allowed_inline = array( 'strong' => array(), 'code' => array() );
	?>

	<?php if ( $needs_install ) : ?>
		<div class="notice notice-warning pixvisualize-install-pro" style="margin:12px 0;padding:12px 16px;border-left:4px solid #dba617;">
			<p><strong><?php esc_html_e( 'Pro license active — install the Pro version to unlock everything.', 'pixvisualize' ); ?></strong></p>
			<p><?php esc_html_e( 'Your license is active on the Free code: the Pro controls stay hidden until pixvisualize-pro is installed (otherwise they would look working in the editor but do nothing on the site).', 'pixvisualize' ); ?></p>
			<p><?php esc_html_e( '1) Download pixvisualize-pro.zip from your purchase email or your Freemius Account, 2) go to Extensions → Add → Upload, 3) activate — the Free version deactivates itself.', 'pixvisualize' ); ?>
				<a class="button button-primary" style="margin-left:8px;" href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=upload' ) ); ?>"><?php esc_html_e( 'Upload the Pro version', 'pixvisualize' ); ?></a>
				<?php if ( function_exists( 'pixvisualize_fs' ) && is_callable( array( pixvisualize_fs(), 'get_account_url' ) ) ) : ?>
					<a class="button" style="margin-left:4px;" href="<?php echo esc_url( pixvisualize_fs()->get_account_url() ); ?>"><?php esc_html_e( 'Open my Account', 'pixvisualize' ); ?></a>
				<?php endif; ?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! $is_pro ) : ?>
		<div class="pixvisualize-banner">
			<div class="pixvisualize-banner__text">
				<strong><?php esc_html_e( 'PixVisualize Pro', 'pixvisualize' ); ?></strong> — <?php esc_html_e( 'unlock all the advanced features: native Elementor widget, shortcodes, WooCommerce, magnifier, auto-scan, responsive…', 'pixvisualize' ); ?>
			</div>
			<a class="button button-primary" href="<?php echo esc_url( function_exists( 'pixvisualize_fs' ) ? pixvisualize_fs()->get_upgrade_url() : '#' ); ?>"><?php esc_html_e( 'Go Pro', 'pixvisualize' ); ?></a>
			<a class="button button-secondary" style="margin-left:8px;" href="<?php echo esc_url( function_exists( 'pixvisualize_get_demo_url' ) ? pixvisualize_get_demo_url() : 'https://i-naridigit.com/demo/pixvisualize/' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View Pro demo', 'pixvisualize' ); ?></a>
		</div>
	<?php endif; ?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1"><span class="dashicons dashicons-admin-settings" aria-hidden="true"></span><?php esc_html_e( 'Manage Settings', 'pixvisualize' ); ?></a></li>
		<?php if ( $show_generator ) : ?>
			<li><a href="#tab-2"><span class="dashicons dashicons-editor-code" aria-hidden="true"></span><?php esc_html_e( 'Shortcode Generator', 'pixvisualize' ); ?></a></li>
		<?php endif; ?>
		<li><a href="#tab-<?php echo esc_html( $about_num ); ?>"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span><?php esc_html_e( 'About', 'pixvisualize' ); ?></a></li>
		<?php if ( ! $is_pro ) : ?>
			<li><a href="#tab-<?php echo esc_html( $gopro_num ); ?>"><span class="dashicons dashicons-star-filled" aria-hidden="true"></span><?php esc_html_e( 'Go Pro', 'pixvisualize' ); ?></a></li>
		<?php endif; ?>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<div class="pc-pane">
				<h2><?php esc_html_e( 'Manage Settings', 'pixvisualize' ); ?></h2>
				<p class="pc-pane__intro">
					<?php esc_html_e( 'Enable or disable each PixVisualize feature globally. Block settings (images, labels, position, ratio) are saved by Gutenberg in your post content and are never affected by this page.', 'pixvisualize' ); ?>
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

		<?php if ( $show_generator ) : ?>
			<div id="tab-2" class="tab-pane">
				<div class="pc-pane">
					<h2><?php esc_html_e( 'Shortcode Generator', 'pixvisualize' ); ?></h2>
					<p class="pc-pane__intro">
						<?php echo wp_kses( __( 'Build a <code>[pixvisualize]</code> shortcode visually and copy it anywhere.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?>
					</p>

					<div class="pixvisualize-generator">
						<div class="pixvisualize-subnav" role="tablist" aria-label="<?php esc_attr_e( 'Shortcode Generator', 'pixvisualize' ); ?>">
							<button type="button" class="button pixvisualize-subnav-button is-active" data-subtab="generator" role="tab" aria-selected="true"><?php esc_html_e( 'Shortcode generator', 'pixvisualize' ); ?></button>
							<button type="button" class="button pixvisualize-subnav-button" data-subtab="list" role="tab" aria-selected="false"><?php esc_html_e( 'Shortcode list', 'pixvisualize' ); ?> <span class="pixvisualize-subnav-count">0</span></button>
						</div>
						<div class="pixvisualize-gen-pane" data-pane="generator" role="tabpanel">
						<div class="pixvisualize-generator-layout">
							<div class="pixvisualize-generator-side">
						<h3><?php esc_html_e( '1. Select Images', 'pixvisualize' ); ?></h3>
						<div class="pixvisualize-media-grid">
							<div class="pixvisualize-media-block">
								<div class="pixvisualize-media-thumb" data-preview="before">
									<span class="pixvisualize-media-placeholder">+</span>
								</div>
								<div class="pixvisualize-media-name" data-filename="before"><?php esc_html_e( 'No image selected', 'pixvisualize' ); ?></div>
								<button type="button" class="button button-primary pixvisualize-media-button" data-target="before"><?php esc_html_e( 'Select Image', 'pixvisualize' ); ?></button>
							</div>

							<div class="pixvisualize-media-block">
								<div class="pixvisualize-media-thumb" data-preview="after">
									<span class="pixvisualize-media-placeholder">+</span>
								</div>
								<div class="pixvisualize-media-name" data-filename="after"><?php esc_html_e( 'No image selected', 'pixvisualize' ); ?></div>
								<button type="button" class="button button-primary pixvisualize-media-button" data-target="after"><?php esc_html_e( 'Select Image', 'pixvisualize' ); ?></button>
							</div>
						</div>

						<h3><?php esc_html_e( '2. Configuration', 'pixvisualize' ); ?></h3>
						<h4><?php esc_html_e( 'Content & Labels', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Before Label', 'pixvisualize' ); ?></label>
								<input type="text" data-field="before_label" value="Before">
								<span class="description"><?php esc_html_e( 'Caption displayed on the "Before" side.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'After Label', 'pixvisualize' ); ?></label>
								<input type="text" data-field="after_label" value="After">
								<span class="description"><?php esc_html_e( 'Caption displayed on the "After" side.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="show_labels" value="1" checked> <?php esc_html_e( 'Show Labels', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'Uncheck to hide the "Before" / "After" captions.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Before Label Position', 'pixvisualize' ); ?></label>
								<select data-field="label_position_before">
									<option value="top-left" selected><?php esc_html_e( 'Top Left', 'pixvisualize' ); ?></option>
									<option value="top-right"><?php esc_html_e( 'Top Right', 'pixvisualize' ); ?></option>
									<option value="bottom-left"><?php esc_html_e( 'Bottom Left', 'pixvisualize' ); ?></option>
									<option value="bottom-right"><?php esc_html_e( 'Bottom Right', 'pixvisualize' ); ?></option>
								</select>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'After Label Position', 'pixvisualize' ); ?></label>
								<select data-field="label_position_after">
									<option value="top-left"><?php esc_html_e( 'Top Left', 'pixvisualize' ); ?></option>
									<option value="top-right" selected><?php esc_html_e( 'Top Right', 'pixvisualize' ); ?></option>
									<option value="bottom-left"><?php esc_html_e( 'Bottom Left', 'pixvisualize' ); ?></option>
									<option value="bottom-right"><?php esc_html_e( 'Bottom Right', 'pixvisualize' ); ?></option>
								</select>
							</div>
						</div>

						<h4><?php esc_html_e( 'Caption', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="show_caption" value="1" checked> <?php esc_html_e( 'Show Caption', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'Uncheck to hide the title / text block below the slider.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Title', 'pixvisualize' ); ?></label>
								<input type="text" data-field="caption_title" value="" placeholder="<?php esc_attr_e( 'One object, two realities', 'pixvisualize' ); ?>">
								<span class="description"><?php esc_html_e( 'Bold title displayed below the slider.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Text', 'pixvisualize' ); ?></label>
								<input type="text" data-field="caption_text" value="" placeholder="<?php esc_attr_e( 'See the before and after difference.', 'pixvisualize' ); ?>">
								<span class="description"><?php esc_html_e( 'Small text displayed under the caption title.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Background', 'pixvisualize' ); ?></label>
								<input type="text" data-field="caption_bg" class="pixvisualize-generator-colorpicker" data-default-color="#ffffff" value="#ffffff" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Title Color', 'pixvisualize' ); ?></label>
								<input type="text" data-field="caption_title_color" class="pixvisualize-generator-colorpicker" data-default-color="#1e293b" value="#1e293b" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Text Color', 'pixvisualize' ); ?></label>
								<input type="text" data-field="caption_text_color" class="pixvisualize-generator-colorpicker" data-default-color="#64748b" value="#64748b" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Alignment', 'pixvisualize' ); ?></label>
								<select data-field="caption_alignment">
									<option value="left"><?php esc_html_e( 'Left', 'pixvisualize' ); ?></option>
									<option value="center" selected><?php esc_html_e( 'Center', 'pixvisualize' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'pixvisualize' ); ?></option>
								</select>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Title Size (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="caption_title_size" min="12" max="48" value="22">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Text Size (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="caption_text_size" min="10" max="32" value="15">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Padding X (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="caption_padding_x" min="0" max="60" value="5">
								<span class="description"><?php esc_html_e( 'Left/right inner spacing of the caption.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Caption Padding Y (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="caption_padding_y" min="0" max="60" value="9">
								<span class="description"><?php esc_html_e( 'Top/bottom inner spacing of the caption: controls its height.', 'pixvisualize' ); ?></span>
							</div>
						</div>

						<h4><?php esc_html_e( 'Layout & Orientation', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Orientation', 'pixvisualize' ); ?></label>
								<select data-field="orientation">
									<option value="horizontal" selected><?php esc_html_e( 'Horizontal', 'pixvisualize' ); ?></option>
									<option value="vertical"><?php esc_html_e( 'Vertical', 'pixvisualize' ); ?></option>
								</select>
								<span class="description"><?php esc_html_e( 'Direction of the sliding cursor (left/right or top/bottom).', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Aspect Ratio', 'pixvisualize' ); ?></label>
								<select data-field="ratio">
									<option value="21/9">21:9</option>
									<option value="2/1">2:1</option>
									<option value="16/9" selected>16:9</option>
									<option value="3/2">3:2</option>
									<option value="4/3">4:3</option>
									<option value="1/1">1:1</option>
									<option value="3/4">3:4</option>
									<option value="2/3">2:3</option>
									<option value="9/16">9:16</option>
									<option value="1/2">1:2</option>
									<option value="9/21">9:21</option>
								</select>
								<span class="description"><?php esc_html_e( 'Width / height proportion, always respected (contain): 21/9 fills the width, 9/21 fills the height.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Slider Height (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="height" min="100" max="2000" value="450">
								<span class="description"><?php esc_html_e( 'Desired zone height, capped by a fixed parent height (contain).', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="full_width" value="1"> <?php esc_html_e( 'Full Width', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'Spans the content width (ratio kept, centered). Useless with a fixed parent height — contain already caps it.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Slider Position (%)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="position" min="0" max="100" value="50">
								<span class="description"><?php esc_html_e( 'Starting position of the divider: 0 = fully "Before", 100 = fully "After".', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Alignment', 'pixvisualize' ); ?></label>
								<select data-field="alignment">
									<option value="left"><?php esc_html_e( 'Left', 'pixvisualize' ); ?></option>
									<option value="center" selected><?php esc_html_e( 'Center', 'pixvisualize' ); ?></option>
									<option value="right"><?php esc_html_e( 'Right', 'pixvisualize' ); ?></option>
								</select>
							</div>
						</div>

						<h4><?php esc_html_e( 'Responsive', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Tablet Position (%)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="tablet_position" min="0" max="100" value="50">
								<span class="description"><?php esc_html_e( 'Divider position on tablets (768px–1024px).', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Mobile Position (%)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="mobile_position" min="0" max="100" value="50">
								<span class="description"><?php esc_html_e( 'Divider position on phones (below 768px).', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Tablet Ratio', 'pixvisualize' ); ?></label>
								<select data-field="tablet_ratio">
									<option value="" selected><?php esc_html_e( 'Inherit (desktop)', 'pixvisualize' ); ?></option>
									<option value="21/9">21:9</option>
									<option value="2/1">2:1</option>
									<option value="16/9">16:9</option>
									<option value="3/2">3:2</option>
									<option value="4/3">4:3</option>
									<option value="1/1">1:1</option>
									<option value="3/4">3:4</option>
									<option value="2/3">2:3</option>
									<option value="9/16">9:16</option>
									<option value="1/2">1:2</option>
									<option value="9/21">9:21</option>
								</select>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Mobile Ratio', 'pixvisualize' ); ?></label>
								<select data-field="mobile_ratio">
									<option value="" selected><?php esc_html_e( 'Inherit (desktop)', 'pixvisualize' ); ?></option>
									<option value="21/9">21:9</option>
									<option value="2/1">2:1</option>
									<option value="16/9">16:9</option>
									<option value="3/2">3:2</option>
									<option value="4/3">4:3</option>
									<option value="1/1">1:1</option>
									<option value="3/4">3:4</option>
									<option value="2/3">2:3</option>
									<option value="9/16">9:16</option>
									<option value="1/2">1:2</option>
									<option value="9/21">9:21</option>
								</select>
							</div>
						</div>

						<h4><?php esc_html_e( 'Handle', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Handle Style', 'pixvisualize' ); ?></label>
								<select data-field="handle">
									<option value="circle" selected><?php esc_html_e( 'Circle', 'pixvisualize' ); ?></option>
									<option value="transparent"><?php esc_html_e( 'Transparent', 'pixvisualize' ); ?></option>
									<option value="line"><?php esc_html_e( 'Line', 'pixvisualize' ); ?></option>
									<option value="arrows"><?php esc_html_e( 'Arrows', 'pixvisualize' ); ?></option>
									<option value="custom"><?php esc_html_e( 'Custom SVG', 'pixvisualize' ); ?></option>
								</select>
								<span class="description"><?php esc_html_e( 'Shape of the cursor that separates the two images.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Custom SVG Icon (<svg> tag…)', 'pixvisualize' ); ?></label>
								<textarea data-field="svg" rows="3" placeholder="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;currentColor&quot;>…</svg>"></textarea>
								<span class="description"><?php esc_html_e( 'Only used with the Custom SVG handle. Paste <svg viewBox="…">…</svg> only: XML prolog, DOCTYPE, metadata and width/height are removed automatically. Use fill="currentColor" so the icon inherits Handle Arrows Color.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Handle Size (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="handle_size" min="16" max="64" value="24">
								<span class="description"><?php esc_html_e( 'Diameter of the handle circle (independent control).', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Handle Arrows Color', 'pixvisualize' ); ?></label>
								<input type="text" data-field="handle_arrow_color" class="pixvisualize-generator-colorpicker" data-default-color="#666666" value="#666666" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
								<span class="description"><?php esc_html_e( 'Color of the arrows inside the handle (independent control).', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Handle Padding (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="handle_padding" min="0" max="20" value="0">
								<span class="description"><?php esc_html_e( 'Inner spacing between the icon and the edge of the handle background, in px.', 'pixvisualize' ); ?></span>
							</div>
						</div>

						<h4><?php esc_html_e( 'Separator', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Separator Thickness (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="separator_thickness" min="1" max="10" value="2">
								<span class="description"><?php esc_html_e( 'Thickness of the vertical bar (independent control).', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Cursor & Handle Color', 'pixvisualize' ); ?></label>
								<input type="text" data-field="separator_color" class="pixvisualize-generator-colorpicker" data-default-color="#ffffff" value="#ffffff" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
								<span class="description"><?php esc_html_e( 'Single synchronized control: background of the vertical bar and the handle circle.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="separator_shadow" value="1" checked> <?php esc_html_e( 'Drop Shadow', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'Grouped shadow on the whole cursor + handle (filter: drop-shadow, no seam at the junction).', 'pixvisualize' ); ?></span>
							</div>
						</div>

						<h4><?php esc_html_e( 'Container Shadow', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="container_shadow" value="1"> <?php esc_html_e( 'Container Shadow', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'A soft shadow is applied to the whole slider.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Shadow Color', 'pixvisualize' ); ?></label>
								<input type="text" data-field="container_shadow_color" class="pixvisualize-generator-colorpicker" data-default-color="#000000" value="#000000" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Horizontal Offset (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_shadow_horizontal" min="-50" max="50" value="0">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Vertical Offset (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_shadow_vertical" min="-50" max="50" value="8">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Blur Radius (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_shadow_blur" min="0" max="60" value="20">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Spread Radius (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_shadow_spread" min="-20" max="40" value="0">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Opacity (0–100)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_shadow_opacity" min="0" max="100" value="35">
							</div>
						</div>

						<h4><?php esc_html_e( 'Container Border', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="container_border" value="1"> <?php esc_html_e( 'Show Border', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'Show a default 2px solid gray border around the container.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Border Width (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_border_width" min="0" max="20" value="0">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Border Style', 'pixvisualize' ); ?></label>
								<select data-field="container_border_style">
									<option value="solid" selected><?php esc_html_e( 'Solid', 'pixvisualize' ); ?></option>
									<option value="dashed"><?php esc_html_e( 'Dashed', 'pixvisualize' ); ?></option>
									<option value="dotted"><?php esc_html_e( 'Dotted', 'pixvisualize' ); ?></option>
									<option value="double"><?php esc_html_e( 'Double', 'pixvisualize' ); ?></option>
								</select>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Border Color', 'pixvisualize' ); ?></label>
								<input type="text" data-field="container_border_color" class="pixvisualize-generator-colorpicker" data-default-color="#000000" value="#000000" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
							</div>

							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="container_rounded" value="1"> <?php esc_html_e( 'Rounded Corners', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'Round all corners with the default 16px radius.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Top Left Radius (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_border_radius_tl" min="0" max="100" value="0">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Top Right Radius (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_border_radius_tr" min="0" max="100" value="0">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Bottom Right Radius (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_border_radius_br" min="0" max="100" value="0">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Bottom Left Radius (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="container_border_radius_bl" min="0" max="100" value="0">
							</div>
						</div>

						<h4><?php esc_html_e( 'Label Style', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Label Background', 'pixvisualize' ); ?></label>
								<input type="text" data-field="label_bg" class="pixvisualize-generator-colorpicker" data-default-color="#00000080" value="#00000080" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Text Color', 'pixvisualize' ); ?></label>
								<input type="text" data-field="label_color" class="pixvisualize-generator-colorpicker" data-default-color="#ffffff" value="#ffffff" placeholder="#RRGGBB / #RRGGBBAA" maxlength="9">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Label Font Size (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="label_font_size" min="10" max="24" value="12">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Label Font Weight', 'pixvisualize' ); ?></label>
								<input type="number" data-field="label_font_weight" min="400" max="900" step="100" value="600">
								<span class="description"><?php esc_html_e( '400 = normal, 700 = bold.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Label Padding X (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="label_padding_x" min="0" max="30" value="10">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Label Padding Y (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="label_padding_y" min="0" max="30" value="5">
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Label Border Radius (px)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="label_border_radius" min="0" max="20" value="4">
							</div>
						</div>

						<h4><?php esc_html_e( 'Interactions', 'pixvisualize' ); ?></h4>
						<div class="pixvisualize-config-grid">
							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="hover" value="1"> <?php esc_html_e( 'Move on Hover', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'On hover, the cursor moves under the mouse, without clicking.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="autoscan" value="1"> <?php esc_html_e( 'Auto-Slide Animation', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'The cursor sweeps from 100% to 0% then back to the initial position when the slider becomes visible.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Auto-Slide Duration (ms)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="autoscan_duration" min="1000" max="10000" step="100" value="2500">
							</div>

							<div class="pixvisualize-field">
								<label><input type="checkbox" data-field="magnifier" value="1"> <?php esc_html_e( 'Magnifier', 'pixvisualize' ); ?></label>
								<span class="description"><?php esc_html_e( 'A magnifier enlarges the hovered area of the image.', 'pixvisualize' ); ?></span>
							</div>

							<div class="pixvisualize-field">
								<label><?php esc_html_e( 'Magnifier Zoom (x)', 'pixvisualize' ); ?></label>
								<input type="number" data-field="magnifier_zoom" min="1.5" max="4" step="0.5" value="2">
							</div>
						</div>
							</div><!-- .pixvisualize-generator-side -->
							<div class="pixvisualize-generator-main">
						<h3><?php esc_html_e( 'Live Preview', 'pixvisualize' ); ?></h3>
						<div class="pixvisualize-generator-preview-wrap">
						<div class="pixvisualize-generator-card">
						<div
							class="pixvisualize-generator-preview pixvisualize-elementor-preview pixvisualize-elementor-preview--empty"
							data-orientation="horizontal"
							data-hover-mode="0"
							data-auto-scan="0"
							data-auto-scan-duration="2500"
							data-magnifier="0"
							data-magnifier-zoom="2"
							style="aspect-ratio: 16/9; --pc-pos: 50%;"
						>
							<img class="pixvisualize-elementor-preview__image pixvisualize-elementor-preview__after" src="" alt="" style="display: none;">
							<img class="pixvisualize-elementor-preview__image pixvisualize-elementor-preview__before" src="" alt="" style="display: none;">
							<div class="pixvisualize-elementor-preview__handle pixvisualize-elementor-preview__handle--horizontal" style="display: none;">
								<div class="pixvisualize-elementor-preview__handle-line"></div>
								<div class="pixvisualize-elementor-preview__handle-icon"><span class="pixvisualize-elementor-preview__arrows" style="width: 20px; height: 20px;"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l-5 6 5 6"/><path d="M15 6l5 6-5 6"/></svg></span></div>
							</div>
							<span class="pixvisualize-elementor-preview__label pixvisualize-elementor-preview__label-before" style="display: none;"><?php esc_html_e( 'Before', 'pixvisualize' ); ?></span>
							<span class="pixvisualize-elementor-preview__label pixvisualize-elementor-preview__label-after" style="display: none;"><?php esc_html_e( 'After', 'pixvisualize' ); ?></span>
							<span class="pixvisualize-generator-preview__empty"><?php esc_html_e( 'Select your Before and After images to display the comparison slider.', 'pixvisualize' ); ?></span>
						</div>
							<div class="pixvisualize-generator-caption" style="display: none;">
								<div class="pixvisualize-generator-caption-title"></div>
								<div class="pixvisualize-generator-caption-text"></div>
							</div>
						</div>
						</div>
						<p class="pixvisualize-preview-note"><?php esc_html_e( 'Desktop preview. Tablet and mobile positions/ratios apply on smaller screens.', 'pixvisualize' ); ?></p>

						<h3 class="pixvisualize-shortcode-heading"><?php esc_html_e( '3. Get Shortcode', 'pixvisualize' ); ?></h3>
						<div class="pixvisualize-code-row">
							<input type="text" class="pixvisualize-shortcode-field" readonly>
							<button type="button" class="button button-primary button-small pixvisualize-icon-button pixvisualize-copy-button" data-tip="<?php esc_attr_e( 'Copy Shortcode', 'pixvisualize' ); ?>" aria-label="<?php esc_attr_e( 'Copy Shortcode', 'pixvisualize' ); ?>" disabled><span class="dashicons dashicons-clipboard" aria-hidden="true"></span></button>
							<button type="button" class="button button-small pixvisualize-icon-button pixvisualize-save-button" data-tip="<?php esc_attr_e( 'Save to list', 'pixvisualize' ); ?>" aria-label="<?php esc_attr_e( 'Save to list', 'pixvisualize' ); ?>" disabled><span class="dashicons dashicons-saved" aria-hidden="true"></span></button>
							<button type="button" class="button button-small pixvisualize-icon-button pixvisualize-clear-button" data-tip="<?php esc_attr_e( 'Clear Images', 'pixvisualize' ); ?>" aria-label="<?php esc_attr_e( 'Clear Images', 'pixvisualize' ); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button>
							<button type="button" class="button button-small pixvisualize-icon-button pixvisualize-reset-button" data-tip="<?php esc_attr_e( 'Reset', 'pixvisualize' ); ?>" aria-label="<?php esc_attr_e( 'Reset', 'pixvisualize' ); ?>"><span class="dashicons dashicons-update" aria-hidden="true"></span></button>
						</div>

						<details class="pixvisualize-import-row">
							<summary class="pixvisualize-import-title"><?php esc_html_e( 'Edit an existing shortcode', 'pixvisualize' ); ?></summary>
							<p class="pixvisualize-import-text"><?php esc_html_e( 'Paste a shortcode you copied earlier to reload it into the generator (images + settings + preview), edit it and copy it again.', 'pixvisualize' ); ?></p>
							<div class="pixvisualize-import-inline">
								<textarea class="pixvisualize-import-field" rows="1" placeholder='[pixvisualize before="..." after="..." ratio="16/9"]'></textarea>
								<button type="button" class="button button-secondary pixvisualize-import-button"><?php esc_html_e( 'Load into the generator', 'pixvisualize' ); ?></button>
							</div>
							<span class="pixvisualize-import-message" aria-live="polite"></span>
						</details>
							</div><!-- .pixvisualize-generator-main -->
						</div><!-- .pixvisualize-generator-layout -->
						</div><!-- .pixvisualize-gen-pane[data-pane="generator"] -->
						<div class="pixvisualize-gen-pane" data-pane="list" role="tabpanel" hidden>
							<p class="pixvisualize-saved-empty"><?php esc_html_e( 'No saved shortcodes yet. Build one in the generator and click the save icon to keep it here.', 'pixvisualize' ); ?></p>
							<div class="pixvisualize-saved-list"></div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div id="tab-<?php echo esc_html( $about_num ); ?>" class="tab-pane">
			<div class="pc-pane">
				<h2><?php esc_html_e( 'About', 'pixvisualize' ); ?></h2>

				<?php if ( $is_pro ) : ?>
					<p>
						<?php echo wp_kses( __( '<strong>PixVisualize</strong> is the complete Before/After image comparison suite for WordPress. Compare two images with an interactive sliding cursor through a native Gutenberg block, a dedicated Elementor widget, shortcodes and full WooCommerce integration.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?>
					</p>
				<?php else : ?>
					<p>
						<strong><?php esc_html_e( 'PixVisualize', 'pixvisualize' ); ?></strong> <?php esc_html_e( 'provides an interactive Gutenberg block to compare two images (Before/After) with an overlaid sliding cursor.', 'pixvisualize' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'Built natively with', 'pixvisualize' ); ?> <strong><?php esc_html_e( 'React', 'pixvisualize' ); ?></strong> <?php esc_html_e( 'and the @wordpress/scripts ecosystem, the block works both in the editor (back-office) and on the public site (front-office).', 'pixvisualize' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'The settings of each block (images, labels, position, ratio) are saved by Gutenberg in the content of your posts and pages: they are never affected by this settings page.', 'pixvisualize' ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $is_pro ) : ?>
				<h3><?php esc_html_e( 'Pro Features', 'pixvisualize' ); ?></h3>
				<div class="pc-pro-features">
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-layout" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Advanced Elementor widget</strong> with visual controls and a live WYSIWYG preview.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-admin-customizer" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Custom handle styles</strong>: circle, transparent, line, double arrows or your own SVG icon, with independent size, arrows color and padding.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-tag" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Advanced labels</strong>: position, typography, colors and margins.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-art" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Cursor & handle</strong>: one synchronized color for the bar and the circle, plus a grouped drop shadow with no seam.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-image-flip-vertical" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Vertical orientation</strong> (Top/Bottom) in addition to horizontal.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-smartphone" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Responsive per breakpoint</strong>: tablet/mobile position and ratio.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-move" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Hover Mode</strong>: the cursor follows the pointer.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-controls-play" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Auto-Scan</strong> intro animation.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-search" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Magnifier Glass</strong> zoom effect.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-editor-code" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Shortcode generator</strong>: build a <code>[pixvisualize]</code> shortcode visually, keep a saved list and reload any shortcode for editing.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-cart" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>WooCommerce support</strong>: Before/After gallery on single product pages.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-editor-paragraph" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Custom captions</strong>: title + text with background, colors, alignment, sizes and padding.', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-admin-appearance" aria-hidden="true"></span>
						<p><?php echo wp_kses( __( '<strong>Container style</strong>: custom shadow, border and per-corner radius (free ON/OFF toggles included).', 'pixvisualize' ), $pixvisualize_allowed_inline ); ?></p>
					</div>
				</div>

				<h3><?php esc_html_e( 'Settings Manager', 'pixvisualize' ); ?></h3>
				<p>
					<?php esc_html_e( 'The toggles above activate or deactivate each feature globally. Block settings (images, labels, position, ratio) are saved by Gutenberg in your post content and are never affected by this page. When an option is missing (fresh install), every feature stays active by default for full backward compatibility.', 'pixvisualize' ); ?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( ! $is_pro ) : ?>
		<div id="tab-<?php echo esc_html( $gopro_num ); ?>" class="tab-pane">
			<div class="pc-pane">
				<h2><?php esc_html_e( 'Go Pro', 'pixvisualize' ); ?></h2>
				<p class="pc-pane__intro">
					<?php esc_html_e( 'PixVisualize Pro extends the Free block with advanced controls, additional rendering modes and dedicated integrations — all with the same intuitive Gutenberg experience.', 'pixvisualize' ); ?>
				</p>

				<table class="pc-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Feature', 'pixvisualize' ); ?></th>
							<th class="pc-table__free-col"><?php esc_html_e( 'Free', 'pixvisualize' ); ?></th>
							<th class="pc-table__pro-col"><?php esc_html_e( 'Pro', 'pixvisualize' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Comparateur Avant / Après', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Libellés Avant / Après personnalisables', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Affichage / masquage des libellés', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Orientation horizontale', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Orientation verticale', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Format et hauteur personnalisables', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Position initiale du curseur', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Pleine largeur et alignement', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Légende sous le comparateur', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Personnalisation complète de la légende', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Design responsive avancé', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Personnalisation de la poignée et du séparateur', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Libellés entièrement personnalisables', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Bordures et coins personnalisables', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Ombre personnalisable', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Déplacement au survol', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Balayage automatique', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Loupe avec zoom', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Shortcodes sauvegardés', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Modification visuelle des shortcodes existants', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Bloc Gutenberg', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Réglages avancés Gutenberg', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Widget Elementor', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Style et prévisualisation WYSIWYG Elementor', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
						<tr>
							<td class="pc-feature"><strong><?php esc_html_e( 'Galerie Avant / Après WooCommerce', 'pixvisualize' ); ?></strong></td>
							<td><span class="pc-badge pc-badge--no"><span class="pc-badge__icon">&mdash;</span> <?php esc_html_e( 'No', 'pixvisualize' ); ?></span></td>
							<td><span class="pc-badge pc-badge--yes"><span class="pc-badge__icon">&#10003;</span> <?php esc_html_e( 'Yes', 'pixvisualize' ); ?></span></td>
						</tr>
					</tbody>
				</table>

				<h3 class="pc-pane__section-title"><?php esc_html_e( 'Why upgrade?', 'pixvisualize' ); ?></h3>
				<div class="pc-pro-features">
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-clock"></span>
						<strong><?php esc_html_e( 'Save hours', 'pixvisualize' ); ?></strong>
						<p><?php esc_html_e( 'Embed comparisons anywhere with shortcodes and the WooCommerce gallery — no block editor needed.', 'pixvisualize' ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-awards"></span>
						<strong><?php esc_html_e( 'Impress clients', 'pixvisualize' ); ?></strong>
						<p><?php esc_html_e( 'Hover mode, magnifier glass and the Auto-Scan intro make every demo memorable.', 'pixvisualize' ); ?></p>
					</div>
					<div class="pc-pro-feature">
						<span class="pc-pro-feature__icon dashicons dashicons-admin-customizer"></span>
						<strong><?php esc_html_e( 'Full control', 'pixvisualize' ); ?></strong>
						<p><?php esc_html_e( 'A native Elementor widget with visual styling, vertical orientation and per-breakpoint responsive settings.', 'pixvisualize' ); ?></p>
					</div>
				</div>

				<div class="pc-callouts">
					<div class="pc-callout pc-callout--migration">
						<strong><?php esc_html_e( 'Zero-effort upgrade', 'pixvisualize' ); ?></strong>
						<p><?php esc_html_e( 'Same block name — your existing blocks keep working after switching to Pro, no re-editing.', 'pixvisualize' ); ?></p>
					</div>
					<div class="pc-callout pc-callout--guarantee">
						<strong><?php esc_html_e( 'Priority support included', 'pixvisualize' ); ?></strong>
						<p><?php esc_html_e( 'Dedicated support: get fast help to configure and integrate your comparisons without technical roadblocks.', 'pixvisualize' ); ?></p>
					</div>
				</div>

				<div class="pc-pane__cta">
					<a class="button button-primary button-hero" href="<?php echo esc_url( function_exists( 'pixvisualize_fs' ) ? pixvisualize_fs()->get_upgrade_url() : '#' ); ?>"><?php esc_html_e( 'Go Pro now', 'pixvisualize' ); ?></a>
					<a class="button button-secondary button-hero" style="margin-left:8px;" href="<?php echo esc_url( function_exists( 'pixvisualize_get_demo_url' ) ? pixvisualize_get_demo_url() : 'https://i-naridigit.com/demo/pixvisualize/' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View Pro demo', 'pixvisualize' ); ?></a>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>