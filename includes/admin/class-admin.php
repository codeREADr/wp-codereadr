<?php
/**
 * Admin: class Admin
 *
 * @since 1.0.0
 * @package CodeReadr
 * @subpackage Admin
 */

namespace CodeReadr\Admin;

use CodeReadr\Services_Model;

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
		add_action( 'wp_ajax_codereadr_insert_or_update_service', array( $this, 'insert_or_update_service' ) );
		add_action( 'wp_ajax_codereadr_delete_service', array( $this, 'delete_service' ) );
		add_action( 'wp_ajax_codereadr_retrieve_users', array( $this, 'retrieve_users' ) );
		add_action( 'wp_ajax_codereadr_create_new_user', array( $this, 'create_new_user' ) );
		add_action( 'wp_ajax_codereadr_save_users_list', array( $this, 'save_users_list' ) );
	}


	/**
	 * Add a new response via ajax
	 *
	 * @since 1.0.0
	 */
	public function insert_or_update_service() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'codereadr-nonce' ) ) {
			// This nonce is not valid.
			die( __( 'Security check', 'codereadr' ) );
		}
		$action_name                             = sanitize_text_field( $_POST['formData']['codereadr-service-action-select'] );
		$title                                   = sanitize_text_field( $_POST['formData']['codereadr-service-name'] );
		$integration_slug                        = sanitize_text_field( $_POST['formData']['codereadr-service-integration-slug'] );
		$codereadr_service_id                    = (int) $_POST['formData']['codereadr-service-remote-id'];
		$service_id                              = (int) $_POST['formData']['service-database-unique-id'];
		$unsanitized_default_invalid_conditions  = $_POST['formData']['default_invalid_conditions'];
		$unsanitized_optional_invalid_conditions = $_POST['formData']['optional_invalid_conditions'];

		$default_invalid_conditions  = array();
		$optional_invalid_conditions = array();

		foreach ( $unsanitized_optional_invalid_conditions as $key => $value ) {
			$optional_invalid_conditions[ sanitize_key( $key ) ] = array(
				'response_text' => stripslashes( sanitize_textarea_field( $value['response_text'] ) ),
				'checkbox'      => (bool) $value['checkbox'],
			);
		}

		foreach ( $unsanitized_default_invalid_conditions as $key => $value ) {
			$default_invalid_conditions[ sanitize_key( $key ) ] = array(
				'response_text' => stripslashes( sanitize_textarea_field( $value['response_text'] ) ),
			);

		}

		$meta = array_merge(
			array(
				'success_response_txt' => stripslashes( sanitize_textarea_field( $_POST['formData']['codereadr-service-response-text'] ) ),
			),
			array(
				'default_invalid_conditions'  => $default_invalid_conditions,
				'optional_invalid_conditions' => $optional_invalid_conditions,
			)
		);

		if ( $service_id && $codereadr_service_id ) {
			$request = wp_remote_get(
				'https://api.codereadr.com/api/',
				array(
					'body' => array(
						'api_key'           => esc_attr( get_option( 'codereadr-api-key' ) ),
						'section'           => 'services',
						'action'            => 'update',
						'validation_method' => 'postback',
						'postback_url'      => site_url( '?codereadr_postback_listener=true' ),
						'service_name'      => $title,
						'service_id'        => $codereadr_service_id,
					),
				)
			);
		} else {
			$request = wp_remote_get(
				'https://api.codereadr.com/api/',
				array(
					'body' => array(
						'api_key'           => esc_attr( get_option( 'codereadr-api-key' ) ),
						'section'           => 'services',
						'action'            => 'create',
						'validation_method' => 'postback',
						'postback_url'      => site_url( '?codereadr_postback_listener=true' ),
						'service_name'      => $title,
					),
				)
			);
		}
		$response = wp_remote_retrieve_body( $request );

		if ( $response ) {

			$response = simplexml_load_string( $response );
			$response = codereadr_xml2array( $response );
			if ( $response['status'] ) {
				if ( $response['status'] !== '0' ) {
					if ( $service_id ) {
						$res = Services_Model::update_service( $service_id, $title, $action_name, $integration_slug, $meta );
					} else {
						$res = Services_Model::add_new_service( $title, $response['id'], $action_name, $integration_slug, $meta );
					}
					if ( $res ) {
						wp_send_json_success( array( 'message' => 'Inserted succesfully!' ) );
					} else {
						wp_send_json_error( array( 'message' => 'Error while inserting a new service' ) );
					}
				}
			} else {
				if ( $response['status'] === '0' ) {
					wp_send_json_error( array( 'message' => $response['error'] ) );
				}

				wp_send_json_error( array( 'message' => 'Error while creating the service!' ) );
			}
		}
	}

	/**
	 * Delete service
	 *
	 * @since 1.0.0
	 */
	public function delete_service() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'codereadr-nonce' ) ) {
			// This nonce is not valid.
			die( __( 'Security check', 'codereadr' ) );
		}
		$codereadr_service_id = (int) $_POST['codereadr-service-remote-id'];
		$service_id           = (int) $_POST['service-database-unique-id'];
		$request              = wp_remote_get(
			'https://api.codereadr.com/api/',
			array(
				'body' => array(
					'api_key'    => esc_attr( get_option( 'codereadr-api-key' ) ),
					'section'    => 'services',
					'action'     => 'delete',
					'service_id' => $codereadr_service_id,
				),
			)
		);

		$response = wp_remote_retrieve_body( $request );
		if ( $response ) {
			$response = simplexml_load_string( $response );
			$response = codereadr_xml2array( $response );
			if ( $response['status'] ) {

				$res = Services_Model::delete_service( $service_id );
				if ( $res ) {
					wp_send_json_success( array( 'message' => 'Deleted succesfully!' ) );
				} else {
					wp_send_json_error( array( 'message' => 'Error while deleting the service' ) );
				}
			}
		}

	}

	/**
	 * Enqueue admin enqueue_admin_styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( 'codereadr-admin-css', CODEREADR_PLUGIN_URL . 'includes/admin/assets/style.css', array(), '1.0' );
		wp_register_style( 'codereadr-highlight-css', CODEREADR_PLUGIN_URL . 'includes/admin/assets/highlight.css', array(), '1.0' );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'codereadr-admin-js', CODEREADR_PLUGIN_URL . 'includes/admin/assets/scripts.js', array( 'jquery' ), '1.0' );
		wp_register_script( 'codereadr-highlight-js', CODEREADR_PLUGIN_URL . 'includes/admin/assets/highlight.js', array( 'jquery', 'codereadr-admin-js' ), '1.0' );
		wp_localize_script(
			'codereadr-admin-js',
			'codeReadr',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'codereadr-nonce' ),
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
			'data:image/svg+xml;base64,' . base64_encode(
				'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27.02 27.01">
					<style>
						.codereadr-svg-icon-path {
							fill: #a9abae; fill-rule:nonzero
						}
					</style>
					<g id="Layer_2" data-name="Layer 2">
						<g id="Layer_1-2" data-name="Layer 1">
							<g id="Group_1" data-name="Group 1">
								<path class="codereadr-svg-icon-path" id="Exclusion_1" data-name="Exclusion 1" d="M13.51,27A13.5,13.5,0,1,1,18.76,26,13.5,13.5,0,0,1,13.51,27Zm0-24.74a11.22,11.22,0,1,0,4.37.88A11.23,11.23,0,0,0,13.51,2.27Z"/>
								<path class="codereadr-svg-icon-path" id="Exclusion_2" data-name="Exclusion 2" d="M9.26,11.58a11.54,11.54,0,0,1-2.2-.19,2.4,2.4,0,0,1-1.5-1,1.26,1.26,0,0,1-.16-1c.14-.5,1.37-1.32,2.94-1.95A18,18,0,0,1,14.9,6.22h.42c1,0,1.61.34,1.82,1a1.69,1.69,0,0,1-.55,1.74l-.18.14h0a11.63,11.63,0,0,1-4.5,2.21A11.28,11.28,0,0,1,9.26,11.58ZM10,9.41c-.7,0-2.25.05-3.13.08a.6.6,0,0,0-.53.35.51.51,0,0,0,.07.55,1.49,1.49,0,0,0,1,.5c.44,0,1.33.09,2.11.09a8,8,0,0,0,1.2-.06c.12,0,.44-.08.53-.27a.84.84,0,0,0-.1-.78,1.12,1.12,0,0,0-.86-.45Z"/>
								<path class="codereadr-svg-icon-path" data-name="Path 2" d="M16.16,9.65a.17.17,0,0,1,.23,0,.36.36,0,0,1,0,.09,3.65,3.65,0,0,0,.38,1.41c.34.55,2.26,5.57,2.73,6.55a3.09,3.09,0,0,1-.31,3.05,2.25,2.25,0,0,1-2.69.69c-1.3-.5-.81-1.26-.68-1.37s.74-.81.66-1.26a34.61,34.61,0,0,0-2.8-4.48c-.5-.6-.81-.3-1.15-.66s0-1-.19-1.39a1.67,1.67,0,0,0-.31-.44.17.17,0,0,1,0-.23l.08,0A10.74,10.74,0,0,0,16.16,9.65Z"/>
							</g>
						</g>
					</g>
				</svg>'
			),
			30
		);
		add_submenu_page( 'codereadr-settings', __( 'CodeREADr', 'codereadr' ), __( 'Settings', 'codereadr' ), 'manage_options', 'codereadr-settings', array( $this, 'render_settings_page' ) );
		add_submenu_page( 'codereadr-settings', __( 'Services', 'codereadr' ), __( 'Services', 'codereadr' ), 'manage_options', 'codereadr-services', array( $this, 'render_services_page' ) );
		add_submenu_page( 'codereadr-settings', __( 'Integrations', 'codereadr' ), __( 'Integrations', 'codereadr' ), 'manage_options', 'codereadr-integrations', array( $this, 'render_integrations_page' ) );
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
	 * Render services page
	 *
	 * @since 1.0.0
	 */
	public function render_integrations_page() {
		ob_start();
		require_once CODEREADR_PLUGIN_DIR . 'includes/admin/pages/integrations.php';
		echo ob_get_clean();
	}

	/**
	 * Retrieve user by id.
	 *
	 * @since 1.0.0
	 */
	public function retrieve_users() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'codereadr-nonce' ) ) {
			// This nonce is not valid.
			die( __( 'Security check', 'codereadr' ) );
		}
		$request = wp_remote_get(
			'https://api.codereadr.com/api/',
			array(
				'body' => array(
					'api_key' => esc_attr( get_option( 'codereadr-api-key' ) ),
					'section' => 'users',
					'action'  => 'retrieve',
				),
			)
		);

		$response = wp_remote_retrieve_body( $request );
		if ( $response ) {
			$response = json_decode( wp_json_encode( (array) simplexml_load_string( $response ) ), 1 );

			if ( $response['status'] ) {
				$users = $response['user'];
				wp_send_json_success(
					array(
						'message' => 'Retrieved succesfully!',
						'users'   => $users,
					)
				);
			}
		}
	}

	/**
	 * Create a new user
	 *
	 * @since 1.0.0
	 */
	public function create_new_user() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'codereadr-nonce' ) ) {
			// This nonce is not valid.
			die( __( 'Security check', 'codereadr' ) );
		}
		$username = sanitize_text_field( $_POST['username'] );

		$password = $_POST['userPass'];
		$request  = wp_remote_get(
			'https://api.codereadr.com/api/',
			array(
				'body' => array(
					'api_key'  => esc_attr( get_option( 'codereadr-api-key' ) ),
					'section'  => 'users',
					'action'   => 'create',
					'username' => $username,
					'password' => $password,
				),
			)
		);

		$response = wp_remote_retrieve_body( $request );

		if ( $response ) {
			$response = simplexml_load_string( $response );
			$response = codereadr_xml2array( $response );
			if ( $response['status'] ) {
				$request  = wp_remote_get(
					'https://api.codereadr.com/api/',
					array(
						'body' => array(
							'api_key'    => esc_attr( get_option( 'codereadr-api-key' ) ),
							'section'    => 'services',
							'action'     => 'adduserpermission',
							'user_id'    => $response['id'],
							'service_id' => (int) $_POST['serviceId'],
						),
					)
				);
				$response = wp_remote_retrieve_body( $request );
				if ( $response ) {
					$response = simplexml_load_string( $response );
					$response = codereadr_xml2array( $response );
					if ( $response['status'] ) {
						wp_send_json_success( array( 'message' => 'Created succesfully!' ) );
					} else {
						if ( $response['status'] === '0' ) {
							wp_send_json_error( array( 'message' => $response['error'] ) );
						} else {
							wp_send_json_error( array( 'message' => 'Error while creating user!' ) );
						}
					}
				}
			} else {
				if ( $response['status'] === '0' ) {
					wp_send_json_error( array( 'message' => $response['error'] ) );
				} else {
					wp_send_json_error( array( 'message' => 'Error while creating user!' ) );
				}
			}
		}

	}

	/**
	 * Save users list
	 *
	 * @since 1.0.0
	 */
	public function save_users_list() {
		$nonce   = $_POST['nonce'];
		$api_key = esc_attr( get_option( 'codereadr-api-key' ) );
		if ( ! wp_verify_nonce( $nonce, 'codereadr-nonce' ) ) {
			// This nonce is not valid.
			die( __( 'Security check', 'codereadr' ) );
		}
		$form_data  = $_POST['formData'];
		$service_id = (int) $_POST['serviceId'];
		$new_users  = array();
		foreach ( $form_data as $item ) {
			$new_users[] = (int) $item['name'];
		}
		$request  = wp_remote_get(
			'https://api.codereadr.com/api/',
			array(
				'body' => array(
					'api_key'    => $api_key,
					'section'    => 'services',
					'action'     => 'retrieve',
					'service_id' => $service_id,
				),
			)
		);
		$response = wp_remote_retrieve_body( $request );

		if ( $response ) {
			$response = json_decode( wp_json_encode( (array) simplexml_load_string( $response ) ), 1 );

			if ( $response['status'] ) {
				$users = array();
				foreach ( $response['service']['user'] as $user ) {
					$users[] = (int) $user['@attributes']['id'];
				}

				$new_deauthorized_users = array_values( array_diff( $users, $new_users ) );
				$new_authorized_users   = array_values( array_diff( $new_users, $users ) );
				$request                = wp_remote_get(
					'https://api.codereadr.com/api/',
					array(
						'body' => array(
							'api_key'    => $api_key,
							'section'    => 'services',
							'action'     => 'adduserpermission',
							'service_id' => $service_id,
							'user_id'    => implode( ',', $new_authorized_users ),
						),
					)
				);
				$request                = wp_remote_get(
					'https://api.codereadr.com/api/',
					array(
						'body' => array(
							'api_key'    => $api_key,
							'section'    => 'services',
							'action'     => 'revokeuserpermission',
							'service_id' => $service_id,
							'user_id'    => implode( ',', $new_deauthorized_users ),
						),
					)
				);
				wp_send_json_success(
					array(
						'message' => 'Updated Successfully!',
					)
				);
			}
		}
	}

}
