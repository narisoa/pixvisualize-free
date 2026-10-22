<?php
/**
 * @package  PixVisualize
 */
namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * Registers the PixVisualize Gutenberg block.
 *
 * The block attributes are stored by Gutenberg inside the post content
 * (block comment delimiters). This controller only registers the block
 * type and never touches the block settings stored on the posts, so the
 * slider configuration always stays intact.
 */
class PixVisualizeController extends BaseController
{
	public function register()
	{
		if ( ! $this->activated( 'pixvisualize_block_manager' ) ) return;

		add_action( 'init', array( $this, 'registerBlock' ) );
		add_filter( 'block_type_metadata_settings', array( $this, 'translateBlockMetadata' ), 10, 2 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'injectEditorPlan' ), 5 );
	}

	/**
	 * Expose le plan Freemius a l'editeur Gutenberg (runtime, pas de marker
	 * possible sur de l'inline JS). Modele 2 plans : 'free' | 'pro'.
	 * 'pro' = licence + code premium (pas de faux Pro sur code Free).
	 */
	public function injectEditorPlan()
	{
		$handle = function_exists( 'generate_block_asset_handle' )
			? generate_block_asset_handle( 'create-block/pixvisualize', 'editorScript' )
			: 'create-block-pixvisualize-editor-script';

		if ( is_array( $handle ) ) {
			return;
		}

		$plan = function_exists( 'pixvisualize_get_plan' ) ? pixvisualize_get_plan() : 'free';
		$upgrade_url = function_exists( 'pixvisualize_fs' ) ? pixvisualize_fs()->get_upgrade_url() : '';
		$demo_url = function_exists( 'pixvisualize_get_demo_url' ) ? pixvisualize_get_demo_url() : 'https://i-naridigit.com/demo/pixvisualize/';
		$needs_install = function_exists( 'pixvisualize_needs_premium_install' ) && pixvisualize_needs_premium_install();

		wp_add_inline_script(
			$handle,
			'window.pixvisualizePlan="' . esc_js( $plan ) . '";' .
			'window.pixvisualizeUpgradeUrl="' . esc_url( $upgrade_url ) . '";' .
			'window.pixvisualizeDemoUrl="' . esc_url( $demo_url ) . '";' .
			'window.pixvisualizeNeedsInstall=' . ( $needs_install ? 'true' : 'false' ) . ';',
			'before'
		);
	}

	/**
	 * Force the translated block title/description so they are localized even
	 * when WordPress does not apply the block.json i18n schema (older WP, block
	 * metadata registry, etc.). The strings resolve through the plugin .mo.
	 *
	 * @param array $settings Block type settings determined from the metadata.
	 * @param array $metadata Block metadata.
	 * @return array
	 */
	public function translateBlockMetadata( $settings, $metadata )
	{
		if ( isset( $metadata['name'] ) && 'create-block/pixvisualize' === $metadata['name'] ) {
			$settings['title']       = _x( 'PixVisualize', 'block title', 'pixvisualize' );
			$settings['description'] = _x( 'A modern image comparison slider block.', 'block description', 'pixvisualize' );
		}

		return $settings;
	}

	public function registerBlock()
	{
		$build_name = function_exists( 'pixvisualize_get_build_dir' ) ? pixvisualize_get_build_dir() : 'build';
		$build_dir  = $this->plugin_path . $build_name;

		if ( ! is_dir( $build_dir ) ) {
			return;
		}

		// Preferred path (WordPress 6.8+): register all block types from the
		// generated blocks-manifest.php collection.
		if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			$manifest = $build_dir . '/blocks-manifest.php';

			if ( file_exists( $manifest ) ) {
				wp_register_block_types_from_metadata_collection( $build_dir, $manifest );
				$this->setScriptTranslations();
				return;
			}
		}

		// Fallback for older WordPress versions: register the block directly
		// from its block.json metadata so the editor always recognises it.
		$block_json = $build_dir . '/pixvisualize/block.json';

		if ( file_exists( $block_json ) ) {
			register_block_type( $block_json );
			$this->setScriptTranslations();
		}
	}

	/**
	 * Register the translations of the block editor script so the JS strings
	 * (wp.i18n) are localized like the PHP ones.
	 */
	private function setScriptTranslations()
	{
		$handle = generate_block_asset_handle( 'create-block/pixvisualize', 'editorScript' );
		wp_set_script_translations( $handle, 'pixvisualize', $this->plugin_path . 'languages/' );
	}
}