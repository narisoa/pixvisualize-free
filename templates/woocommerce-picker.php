<?php
defined( 'ABSPATH' ) || exit;
/**
 * Shared Before/After image picker.
 *
 * Used by both the Product Data panel and the sidebar metabox with the exact
 * same classes + data-* hooks, so the admin script keeps every location in
 * sync in real time.
 *
 * @package  PixVisualize
 *
 * @var string $target   'before' | 'after'
 * @var int    $image_id Selected attachment ID (0 = none).
 */

$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
?>
<div class="pixvisualize-woo-picker">
	<div class="pixvisualize-woo-preview" data-preview="<?php echo esc_attr( $target ); ?>">
		<?php if ( $image_url ) : ?>
			<img src="<?php echo esc_url( $image_url ); ?>" alt="">
		<?php endif; ?>
	</div>

	<input
		type="hidden"
		class="pixvisualize-woo-id"
		name="<?php echo esc_attr( 'pixvisualize_woo_' . $target . '_id' ); ?>"
		data-pixvisualize="<?php echo esc_attr( $target ); ?>"
		value="<?php echo esc_attr( $image_id ); ?>"
	>

	<div class="pixvisualize-woo-actions">
		<button type="button" class="button button-primary pixvisualize-woo-select" data-target="<?php echo esc_attr( $target ); ?>">
			<?php esc_html_e( 'Select Image', 'pixvisualize' ); ?>
		</button>
		<button type="button" class="button pixvisualize-woo-remove pixvisualize-woo-remove-icon" data-remove="<?php echo esc_attr( $target ); ?>" title="<?php esc_attr_e( 'Remove', 'pixvisualize' ); ?>" aria-label="<?php esc_attr_e( 'Remove', 'pixvisualize' ); ?>" <?php echo $image_id ? '' : 'disabled'; ?>>
			<span class="dashicons dashicons-trash" aria-hidden="true"></span>
		</button>
	</div>
</div>