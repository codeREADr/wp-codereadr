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
		$results       = $wpdb->get_results( "select * from {$wpdb->prefix}codereadr_services", 'ARRAY_A' );
		$final_results = array();
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$final_results[] = array(
					'ID'                   => (int) $result['ID'],
					'action_name'          => $result['action_name'],
					'codereadr_service_id' => (int) $result['codereadr_service_id'],
					'title'                => $result['title'],
					'integration_slug'     => $result['integration_slug'],
					'meta'                 => unserialize( $result['meta'] ),
				);
			}
		}
		return $final_results;
	}

	/**
	 * Get service by codereadr_service_id
	 *
	 * @since 1.0.0
	 *
	 * @param int $codereadr_service_id
	 */
	public static function get_service_by_codereadr_service_id( $codereadr_service_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}codereadr_services where codereadr_service_id = %s", (string) $codereadr_service_id ) );
	}

	/**
	 * Insert a new service
	 *
	 * @since 1.0.0
	 *
	 * @param string $title           Service title.
	 * @param string $action_name     Action name.
	 * @param string $details            Service details.
	 *
	 * @return bool|WP_Error
	 */
	public static function add_new_service( $title, $codereadr_service_id, $action_name, $integration_slug, $meta ) {
		global $wpdb;
		$res = $wpdb->insert(
			$wpdb->prefix . 'codereadr_services',
			array(
				'title'                => $title,
				'action_name'          => $action_name,
				'integration_slug'     => $integration_slug,
				'meta'                 => maybe_serialize( $meta ),
				'codereadr_service_id' => $codereadr_service_id,
				'date_created'         => current_time( 'mysql' ),
				'date_updated'         => current_time( 'mysql' ),
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
	 * @param string $meta            Action meta.
	 *
	 * @return bool|WP_Error
	 */
	public static function update_service( $service_id, $title, $action_name, $integration_slug, $meta ) {
		global $wpdb;
		$res = $wpdb->update(
			$wpdb->prefix . 'codereadr_services',
			array(
				'title'            => $title,
				'action_name'      => $action_name,
				'integration_slug' => $integration_slug,
				'meta'             => maybe_serialize( $meta ),
				'date_updated'     => current_time( 'mysql' ),
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
