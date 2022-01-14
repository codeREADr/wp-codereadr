<?php
/**
 * Install: class Install
 *
 * @since 1.0.0
 * @package CodeReadr
 */

namespace CodeReadr;

/**
 * Class Install is responsible for main set up.
 * Also, it creates needed database tables.
 *
 * @since 1.0.0
 */
class Install {

	/**
	 * Init
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
	}

	/**
	 * Check Quill forms version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if ( version_compare( get_option( 'codereadr_version' ), CODEREADR_VERSION, '<' ) ) {
			self::install();
			do_action( 'codereadr_updated' );
		}
	}

	/**
	 * Install CodeReadr
	 *
	 * @since 1.0.0
	 * @static
	 */
	public static function install() {
		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'codereadr_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'codereadr_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::create_tables();
		self::update_codereadr_version();

		delete_transient( 'codereadr_installing' );

	}

	/**
	 * Create DB Tables
	 *
	 * @since 1.0.0
	 */
	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// $charset_collate = $wpdb->get_charset_collate();

		// // dbDelta( $sql );
	}

	/**
	 * Update CodeReadr version to current.
	 *
	 * @since 1.0.0
	 */
	private static function update_codereadr_version() {
		delete_option( 'codereadr_version' );
		add_option( 'codereadr_version', CODEREADR_VERSION );
	}

}
