<?php
/**
 * Postback listener for any Codereadr postback request.
 *
 * @package Codereadr
 */

namespace CodeReadr;

use Codereadr\Services_Model;
use Codereadr\Managers\Actions_Manager;

/**
 * This class should listen for any postback service request
 *
 * @since 1.0.0
 */
class Postback_Listener {

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @var Postback_Listener|null
	 */
	private static $instance = null;

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 1.0.0
	 *
	 * @return Postback_Listener the main instance
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'postback_listener' ) );
	}


	/**
	 * Postback Listener
	 *
	 * @since 1.0.0
	 */
	public function postback_listener() {

		// We don't want to process if it did not come from our postback url
		if ( ! isset( $_GET['codereadr_postback_listener'] ) || ! isset( $_POST['scanid'] ) ) {
			return;
		}

		$scan_data = array();
		foreach ( $_POST as $key => $value ) {
			$key               = sanitize_key( $key );
			$scan_data[ $key ] = sanitize_text_field( $value );
		}

		codereadr_get_logger()->debug( 'received the postback', $scan_data );
		$service_row = Services_Model::get_service_by_codereadr_service_id( $scan_data['sid'] );
		if ( ! $service_row ) {
			$response = array(
				'status' => 0,
				'text'   => 'No service found',
			);
		}

		$action_name  = $service_row->action_name;
		$action_type  = Actions_Manager::instance()->get_registered( $action_name );
		$response     = $action_type->process_action( $scan_data, maybe_unserialize( $service_row->meta ) );
		$response_xml = '<?xml version="1.0" encoding="UTF-8"?>
			<xml>
				<message>
					<status>' . $response['status'] . '</status>
					<text>' . $response['text'] . '</text>
				</message>
			</xml>';

		echo $response_xml;

		die;

		//
		// // Check if  exists that should be there from the service
		// if ( $json->id ) {
		// Do what you want with that json since there is an ID
		// }something
	}


}
Postback_Listener::instance();
