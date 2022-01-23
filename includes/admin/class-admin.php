<?php
/**
 * Admin: class Admin
 *
 * @since 1.0.0
 * @package CodeReadr
 * @subpackage Admin
 */

namespace CodeReadr\Admin;

use CodeReadr\Responses_Model;

/**
 * CodeReadr Admin
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Class Instance.
	 *
	 * @var Admin
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Admin Instance.
	 *
	 * Instantiates or reuses an instance of Admin.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @see Admin()
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
	 * Since this is a singleton class, it is better to have its constructor as a private.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->admin_hooks();
	}

	/**
	 * Admin Hooks.
	 *
	 * @since 1.0.0
	 */
	public function admin_hooks() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_codereadr_insert_or_update_response', array( $this, 'insert_or_update_response' ) );
	}


	/**
	 * Add a new response via ajax
	 *
	 * @since 1.0.0
	 */
	public function insert_or_update_response() {
		$text = sanitize_textarea_field( $_POST['txt'] );
		$name = sanitize_text_field( $_POST['name'] );
		// @todo sanaitization
		$status      = $_POST['status'];
		$response_id = null;
		if ( $_POST['id'] ) {
			$response_id = (int) $_POST['id'];
		}
		if ( $response_id ) {
			$res = Responses_Model::update_response( $response_id, $name, $status, $text );
		} else {
			$res = Responses_Model::add_new_response( $name, $status, $text );
		}
		if ( $res ) {
			wp_send_json_success( array( 'message' => 'Inserted succesfully!' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Error while inserting a new response' ) );
		}
	}

	/**
	 * Enqueue admin enqueue_admin_styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( 'codereadr-admin-css', CODEREADR_PLUGIN_URL . 'includes/admin/assets/style.css', array(), '1.0' );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'codereadr-admin-js', CODEREADR_PLUGIN_URL . 'includes/admin/assets/scripts.js', array( 'jquery' ), '1.0' );
		wp_localize_script(
			'codereadr-admin-js',
			'codeReadr',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Create admin menu pages
	 *
	 * @since 1.0.0
	 */
	public function create_admin_menu_pages() {
		add_menu_page(
			__( 'Settings', 'codereadr' ),
			__( 'CodeREADr', 'codereadr' ),
			'manage_options',
			'codereadr-settings',
			null,
			null,
			30
		);
		add_submenu_page( 'codereadr-settings', __( 'CodeREADr', 'codereadr' ), __( 'Settings', 'codereadr' ), 'manage_options', 'codereadr-settings', array( $this, 'render_settings_page' ) );
		add_submenu_page( 'codereadr-settings', __( 'Services', 'codereadr' ), __( 'Services', 'codereadr' ), 'manage_options', 'codereadr-services', array( $this, 'render_services_page' ) );
		add_submenu_page( 'codereadr-settings', __( 'Actions', 'codereadr' ), __( 'Actions', 'codereadr' ), 'manage_options', 'codereadr-actions', array( $this, 'render_actions_page' ) );
		add_submenu_page( 'codereadr-settings', __( 'Responses', 'codereadr' ), __( 'Responses', 'codereadr' ), 'manage_options', 'codereadr-responses', array( $this, 'render_responses_page' ) );
	}


	/**
	 * Render settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		ob_start();
		require_once CODEREADR_PLUGIN_DIR . 'includes/admin/pages/settings.php';
		echo ob_get_clean();
	}

	/**
	 * Render services page
	 *
	 * @since 1.0.0
	 */
	public function render_services_page() {
		ob_start();
		require_once CODEREADR_PLUGIN_DIR . 'includes/admin/pages/services.php';
		echo ob_get_clean();
	}

	/**
	 * Render actions page
	 *
	 * @since 1.0.0
	 */
	public function render_actions_page() {
		ob_start();
		require_once CODEREADR_PLUGIN_DIR . 'includes/admin/pages/actions.php';
		echo ob_get_clean();
	}

	/**
	 * Render responses page
	 *
	 * @since 1.0.0
	 */
	public function render_responses_page() {
		ob_start();
		require_once CODEREADR_PLUGIN_DIR . 'includes/admin/pages/responses.php';
		echo ob_get_clean();
	}

}
