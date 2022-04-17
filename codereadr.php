<?php
/**
 * Plugin Name:       CodeREADr
 * Plugin URI:        https://www.codereadr.com/
 * Description:       Integrate CodeREADr with WordPress
 * Version:           1.0.0
 * Author:            codereadr.com
 * Author URI:        http://www.codereadr.com
 * Text Domain:       codereadr
 * Requires at least: 4.1
 * Requires PHP: 5.3
 *
 * @package CodeREADr
 */

use CodeReadr\CodeReadr;

defined( 'ABSPATH' ) || exit;

// Plugin file.
if ( ! defined( 'CODEREADR_PLUGIN_FILE' ) ) {
	define( 'CODEREADR_PLUGIN_FILE', __FILE__ );
}

// Plugin version.
if ( ! defined( 'CODEREADR_VERSION' ) ) {
	define( 'CODEREADR_VERSION', '1.7.5' );
}

// Plugin Folder Path.
if ( ! defined( 'CODEREADR_PLUGIN_DIR' ) ) {
	define( 'CODEREADR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL.
if ( ! defined( 'CODEREADR_PLUGIN_URL' ) ) {
	define( 'CODEREADR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Define minimum WP version.
define( 'CODEREADR_MIN_WP_VERSION', '4.1' );

// Define minimun php version.
define( 'CODEREADR_MIN_PHP_VERSION', '5.3' );


// Require autoload.
require_once CODEREADR_PLUGIN_DIR . 'includes/autoload.php';

codereadr_pre_init();


/**
 * Verify that we can initialize CODEREADR , then load it.
 *
 * @since 1.0.0
 */
function codereadr_pre_init() {
	global $wp_version;

	// Get unmodified $wp_version.
	include ABSPATH . WPINC . '/version.php';

	// Strip '-src' from the version string. Messes up version_compare().
	$version = str_replace( '-src', '', $wp_version );

	// Check for minimum WordPress version.
	if ( version_compare( $version, CODEREADR_MIN_WP_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'codereadr_wordpress_version_notice' );
		return;
	}

	// Check for minimum PHP version.
	if ( version_compare( phpversion(), CODEREADR_MIN_PHP_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'codereadr_php_version_notice' );
		return;
	}

	CodeReadr::instance();
	register_activation_hook( CODEREADR_PLUGIN_DIR, array( CodeReadr\Install::class, 'install' ) );

	// do codereadr_loaded action.
	add_action(
		'plugins_loaded',
		function() {
			do_action( 'codereadr_loaded' );
		}
	);
}

/**
 * Display a WordPress version notice and deactivate CODEREADR plugin.
 *
 * @since 1.0.0
 */
function codereadr_wordpress_version_notice() {
	echo '<div class="error"><p>';
	/* translators: %s: Minimum required version */
	printf( __( 'CODEREADR requires WordPress %s or later to function properly. Please upgrade WordPress before activating CODEREADR.', 'codereadr' ), CODEREADR_MIN_WP_VERSION );
	echo '</p></div>';

	deactivate_plugins( 'codereadr/codereadr.php' );
}


/**
 * Display a PHP version notice and deactivate CODEREADR plugin.
 *
 * @since 1.0.0
 */
function codereadr_php_version_notice() {
	echo '<div class="error"><p>';
	/* translators: %s: Minimum required version */
	printf( __( 'CODEREADR requires PHP %s or later to function properly. Please upgrade your PHP version before activating CODEREADR.', 'codereadr' ), CODEREADR_MIN_PHP_VERSION );
	echo '</p></div>';

	deactivate_plugins( 'codereadr/codereadr.php' );
}
