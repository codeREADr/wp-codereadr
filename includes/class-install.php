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
		// if ( version_compare( get_option( 'codereadr_version' ), CODEREADR_VERSION, '<' ) ) {
			self::install();
			do_action( 'codereadr_updated' );
		// }
	}

	/**
	 * Install CodeReadr
	 *
	 * @since 1.0.0
	 * @static
	 */
	public static function install() {
		self::create_tables();
		self::update_codereadr_version();
	}

	/**
	 * Create DB Tables
	 *
	 * @since 1.0.0
	 */
	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}codereadr_services (
			    ID mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				action_id mediumint(8) unsigned NOT NULL,
				response_id mediumint(8) unsigned NOT NULL,
				date_created datetime NOT NULL,
				date_updated datetime,
				PRIMARY KEY  (ID)
			) $charset_collate;
			CREATE TABLE {$wpdb->prefix}codereadr_log (
				ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				timestamp datetime NOT NULL,
				level smallint(4) NOT NULL,
				source varchar(200) NOT NULL,
				message longtext NOT NULL,
				context longtext NULL,
				PRIMARY KEY (ID),
				KEY level (level)
			) $charset_collate;
			CREATE TABLE {$wpdb->prefix}codereadr_actions (
			    ID mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				type varchar(50) NOT NULL,
				details longtext,
				date_created datetime NOT NULL,
				date_updated datetime,
				PRIMARY KEY  (ID)
			) $charset_collate;

			CREATE TABLE {$wpdb->prefix}codereadr_responses (
			    ID mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				status tinyint(1) NOT NULL DEFAULT '1',
				text longtext,
				date_created datetime NOT NULL,
				date_updated datetime,
				PRIMARY KEY  (ID)
			) $charset_collate;";

		 dbDelta( $sql );
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
