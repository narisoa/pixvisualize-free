<?php
/**
 * PixVisualize — Freemius deployment script (CI).
 *
 * Replaces the abandoned `buttonizer/freemius-deploy` Docker action
 * (unbuildable php:7.4 base + removed `::set-output` syntax).
 * Same behavior: upload source ZIP, set release_mode, download the
 * generated free + premium builds, expose their paths as step outputs.
 *
 * Usage (see .github/workflows/deploy-freemius.yml): everything via env.
 * Requires the Freemius API PHP SDK (FS_SDK_DIR, e.g. a fresh
 * `git clone https://github.com/Freemius/freemius-php-sdk.git`).
 */

function fs_env_req( $name ) {
	$value = getenv( $name );
	if ( false === $value || '' === $value ) {
		fwrite( STDERR, "Missing required env: {$name}\n" );
		exit( 1 );
	}
	return $value;
}

function fs_fail( $message, $extra = null ) {
	fwrite( STDERR, $message . "\n" );
	if ( null !== $extra ) {
		fwrite( STDERR, print_r( $extra, true ) . "\n" );
	}
	exit( 1 );
}

$file_name    = fs_env_req( 'FS_FILE_NAME' );
$version      = fs_env_req( 'FS_VERSION' );
$release_mode = getenv( 'FS_RELEASE_MODE' ) ? getenv( 'FS_RELEASE_MODE' ) : 'pending';
$sandbox      = ( getenv( 'FS_SANDBOX' ) === 'true' );
$dev_id       = fs_env_req( 'FS_DEV_ID' );
$public_key   = fs_env_req( 'FS_PUBLIC_KEY' );
$secret_key   = fs_env_req( 'FS_SECRET_KEY' );
$plugin_id    = fs_env_req( 'FS_PLUGIN_ID' );
$plugin_slug  = getenv( 'FS_PLUGIN_SLUG' ) ? getenv( 'FS_PLUGIN_SLUG' ) : $plugin_id;
$sdk_dir      = fs_env_req( 'FS_SDK_DIR' );

if ( ! file_exists( $file_name ) ) {
	fs_fail( "Source ZIP not found: {$file_name}" );
}

$base_file = $sdk_dir . '/freemius/FreemiusBase.php';
$sdk_file  = $sdk_dir . '/freemius/Freemius.php';
if ( ! file_exists( $base_file ) || ! file_exists( $sdk_file ) ) {
	fs_fail( "Freemius API SDK not found under {$sdk_dir} (expected freemius/Freemius.php)." );
}
require_once $base_file;
require_once $sdk_file;

echo "- Deploying {$plugin_slug} to Freemius: version {$version}, release_mode {$release_mode}, sandbox " . ( $sandbox ? 'true' : 'false' ) . "\n";

$api = new Freemius_Api( 'developer', $dev_id, $public_key, $secret_key, $sandbox );
if ( ! is_object( $api ) ) {
	fs_fail( 'Could not initialize Freemius_Api (developer scope). Check DEV_ID / PUBLIC_KEY / SECRET_KEY.' );
}

$tags = $api->Api( 'plugins/' . $plugin_id . '/tags.json', 'GET' );
if ( ! isset( $tags->tags ) || ! is_array( $tags->tags ) ) {
	fs_fail( 'Unexpected response when listing tags.', $tags );
}

if ( isset( $tags->tags[0]->version ) && $tags->tags[0]->version === $version ) {
	$deploy = $tags->tags[0];
	echo "- Version {$version} already deployed (tag {$deploy->id}), reusing it.\n";
} else {
	$deploy = $api->Api(
		'plugins/' . $plugin_id . '/tags.json',
		'POST',
		array( 'add_contributor' => false ),
		array( 'file' => $file_name )
	);
	if ( ! isset( $deploy->id ) ) {
		fs_fail( 'Upload failed (no tag id returned).', $deploy );
	}
	echo "- Uploaded, tag id {$deploy->id}.\n";

	$api->Api(
		'plugins/' . $plugin_id . '/tags/' . $deploy->id . '.json',
		'PUT',
		array( 'release_mode' => $release_mode ),
		array()
	);
	echo "- release_mode set to {$release_mode}.\n";
}

$base     = preg_replace( '/\.zip$/', '', basename( $file_name ) );
$free_zip = $base . '__free.zip';
$pro_zip  = $base . '.zip';

$free_url = $api->GetSignedUrl( 'plugins/' . $plugin_id . '/tags/' . $deploy->id . '.zip', array() );
$pro_url  = $api->GetSignedUrl( 'plugins/' . $plugin_id . '/tags/' . $deploy->id . '.zip?is_premium=true', array() );

$free_bin = @file_get_contents( $free_url );
if ( false === $free_bin ) {
	fs_fail( 'Could not download the free build.' );
}
$pro_bin = @file_get_contents( $pro_url );
if ( false === $pro_bin ) {
	fs_fail( 'Could not download the premium build.' );
}

file_put_contents( $free_zip, $free_bin );
file_put_contents( $pro_zip, $pro_bin );
echo "- Downloaded free build to {$free_zip}.\n";
echo "- Downloaded premium build to {$pro_zip}.\n";

$github_output = getenv( 'GITHUB_OUTPUT' );
if ( $github_output ) {
	file_put_contents( $github_output, "free_version={$free_zip}\npro_version={$pro_zip}\n", FILE_APPEND );
}
