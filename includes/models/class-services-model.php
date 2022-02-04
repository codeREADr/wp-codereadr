<?php
/**
 * Services Modal
 *
 * @package Models
 */

namespace CodeReadr;
/**
 * This class is responsible for retrieving and updating services
 *
 * @since 1.0.0
 */
class Services_Model {

	/**
	 * Get All services
	 *
	 * @since 1.0.0
	 */
	public static function get_services() {
		global $wpdb;
		$sql = "select * from {$wpdb->prefix}codereadr_services";
		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	/**
	 * Insert a new service
	 *
	 * @since 1.0.0
	 *
	 * @param string $title           Service title.
	 * @param string $action_name     Action name.
	 * @param string $response_txt    Response text.
	 *
	 * @return bool|WP_Error
	 */
	public static function add_new_service( $title, $action_name, $response_txt ) {
		global $wpdb;
		$res = $wpdb->insert(
			$wpdb->prefix . 'codereadr_services',
			array(
				'title'        => $title,
				'action_name'  => $action_name,
				'response_txt' => $response_txt,
			)
		);
		return $res;
	}

	/**
	 * Update existing service
	 *
	 * @since 1.0.0
	 *
	 * @param int    $service_id      ID of the service.
	 * @param string $title           Service title.
	 * @param string $action_name     Action name.
	 * @param string $response_txt    Response text.
	 *
	 * @return bool|WP_Error
	 */
	public static function update_service( $service_id, $title, $action_name, $response_txt ) {
		global $wpdb;
		$res = $wpdb->update(
			$wpdb->prefix . 'codereadr_services',
			array(
				'title'        => $title,
				'action_name'  => $action_name,
				'response_txt' => $response_txt,
			),
			array( 'ID' => $service_id )
		);
		return $res;
	}

	/**
	 * Delete service by its id.
	 *
	 * @since 1.0.0
	 *
	 * @param int $service_id The service id.
	 *
	 * @return bool|WP_Error
	 */
	public static function delete_service( $service_id ) {
		global $wpdb;
		$res = $wpdb->delete( $wpdb->prefix . 'codereadr_services', array( 'ID' => $service_id ) );
		return $res;
	}
}
