<?php
defined( 'ABSPATH' ) || exit;
/**
 * Sidebar metabox — quick preview + guide.
 *
 * Full configuration lives in the PixVisualize tab of the Product Data panel.
 * This metabox shows a compact preview and allows fast image selection /
 * removal; both locations share the same postmeta keys and are kept in sync
 * by assets/js/pixvisualize-woocommerce-metabox.js.
 *
 * @package  PixVisualize
 *
 * @var \WP_Post $post Current product post object.
 */

$before_id = absint( get_post_meta( $post->ID, '_pixvisualize_before_image', true ) );
$after_id  = absint( get_post_meta( $post->ID, '_pixvisualize_after_image', true ) );
?>
<div class="pixvisualizeduct-metabox">

	<p class="pixvisualize-woo-note">
		💡 <?php esc_html_e( 'To edit the images, ratio or labels, go to the PixVisualize tab of the "Product data" section.', 'pixvisualize' ); ?>
	</p>

	<div class="pixvisualize-woo-media-grid">
		<div class="pixvisualize-woo-slot">
			<span class="pixvisualize-woo-slot-label"><?php esc_html_e( 'Before', 'pixvisualize' ); ?></span>
			<?php $target = 'before'; $image_id = $before_id; include __DIR__ . '/woocommerce-picker.php'; ?>
		</div>

		<div class="pixvisualize-woo-slot">
			<span class="pixvisualize-woo-slot-label"><?php esc_html_e( 'After', 'pixvisualize' ); ?></span>
			<?php $target = 'after'; $image_id = $after_id; include __DIR__ . '/woocommerce-picker.php'; ?>
		</div>
	</div>

</div>