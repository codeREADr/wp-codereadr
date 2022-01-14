<?php
/**
 * Admin: class Admin
 *
 * @since 1.0.0
 * @package CodeReadr
 * @subpackage Admin
 */

namespace CodeReadr\Admin;

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
	}


	/**
	 * Enqueue admin enqueue_admin_styles
	 *
	 * @since 1.0.0
	 */
	function enqueue_admin_styles( $hook ) {
		wp_enqueue_style( 'codereadr-admin-css', CODEREADR_PLUGIN_URL . 'includes/admin/assets/style.css', array(), '1.0' );
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
