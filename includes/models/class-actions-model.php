<?php
/**
 * This class is responsible for retrieving and updating actions
 */
namespace CodeReadr;

class Actions_Model {

	public static function get_actions() {
		global $wpdb;
		$sql = "select * from {$wpdb->prefix}codereadr_actions";
		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}
}
