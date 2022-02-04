<?php
/**
 * Main class: class CodeReadr
 *
 * @since 1.0.0
 * @package CodeReadr
 */

namespace CodeReadr;

use CodeReadr\Admin\Admin;

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
		// Functions.
		require_once CODEREADR_PLUGIN_DIR . 'includes/functions.php';

		// Models.
		require_once CODEREADR_PLUGIN_DIR . 'includes/models/class-services-model.php';
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
