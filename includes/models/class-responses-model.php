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
}
