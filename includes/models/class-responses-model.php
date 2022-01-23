<?php
/**
 * This class is responsible for retrieving and updating responses
 */

namespace CodeReadr;

class Responses_Model {


	public static function get_responses() {
		global $wpdb;
		$sql = "select * from {$wpdb->prefix}codereadr_responses";
		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public static function add_new_response( $name, $status, $txt ) {
		global $wpdb;
		$res = $wpdb->insert(
			$wpdb->prefix . 'codereadr_responses',
			array(
				'name'   => $name,
				'status' => $status,
				'text'   => $txt,
			)
		);

		return $res;

	}


	public static function update_response( $response_id, $name, $status, $text ) {
		global $wpdb;
		$res = $wpdb->update(
			$wpdb->prefix . 'codereadr_responses',
			array(
				'name'   => $name,
				'status' => $status,
				'text'   => $text,
			),
			array( 'ID' => $response_id )
		);

		return $res;
	}

	public static function delete_response( $response_id ) {
		global $wpdb;
		$res = $wpdb->delete( $wpdb->prefix . 'codereadr_responses', array( 'ID' => $response_id ) );
		return $res;
	}
}
