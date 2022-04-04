<?php
/**
 * Main class: class CodeReadr
 *
 * @since 1.0.0
 * @package CodeReadr
 */

namespace CodeReadr;

use CodeReadr\Admin\Admin;
use CodeReadr\Log_Handlers\Log_Handler_DB;

/**
 * CodeReadr Main Class.
 * The main class that's responsible for loading all dependencies
 *
 * @since 1.0.0
 */
final class CodeReadr {


	/**
	 * Class Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var CodeReadr
	 */
	private static $instance;

	/**
	 * CodeReadr Instance.
	 *
	 * Instantiates or reuses an instance of CodeReadr.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @return self - Single instance
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->hooks();
		$this->init_objects();
	}

	/**
	 * Get readonly property
	 *
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function __get( $name ) {
		return $this->$name;
	}

	/**
	 * Isset for readonly property
	 *
	 * @param string $name Property name.
	 * @return boolean
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Dependencies Loader.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		/**
		 * Interfaces.
		 */
		require_once CODEREADR_PLUGIN_DIR . 'includes/interfaces/class-logger-interface.php';
		require_once CODEREADR_PLUGIN_DIR . 'includes/interfaces/class-log-handler-interface.php';

		// Logger.
		require_once CODEREADR_PLUGIN_DIR . 'includes/class-logger.php';

		// Functions.
		require_once CODEREADR_PLUGIN_DIR . 'includes/functions.php';

		// Models.
		require_once CODEREADR_PLUGIN_DIR . 'includes/models/class-services-model.php';

		// Actions.
		require_once CODEREADR_PLUGIN_DIR . 'includes/actions/class-wc-event-box-search-action.php';
		require_once CODEREADR_PLUGIN_DIR . 'includes/actions/class-wc-event-box-redeam-action.php';
		require_once CODEREADR_PLUGIN_DIR . 'includes/actions/class-event-tickets-search-action.php';
		require_once CODEREADR_PLUGIN_DIR . 'includes/actions/class-event-tickets-redeam-action.php';

		require_once CODEREADR_PLUGIN_DIR . 'includes/class-postback-listener.php';
	}
	private function hooks() {
		add_filter( 'codereadr_register_log_handlers', array( $this, 'register_log_handlers' ) );
	}

	/**
	 * Register log handlers
	 *
	 * @param array $handlers Handlers array to filter.
	 * @return array
	 */
	public function register_log_handlers( $handlers ) {
		$handlers[] = new Log_Handler_DB();
		return $handlers;
	}
	/**
	 * Initialize instances from classes loaded.
	 *
	 * @since 1.0.0
	 */
	private function init_objects() {
		Install::init();
		Admin::instance();
	}

}
