<?php
/**
 * Action for Woocommerce Event Box
 *
 * @package Actions
 */

namespace CodeReadr;
use CodeReadr\Abstracts\Action;
use CodeReadr\Managers\Actions_Manager;
/**
 * This is an action class for Woocommerce Event Box plugin
 *
 * @since 1.0.0
 */
class WC_Box_Office_Search_Action extends Action {

	/**
	 * Action name.
	 * It must be a unique name.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $name = 'wc-box-office-search-action';

	/**
	 * Integration slug
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $integration_slug = 'wc-box-office';

	/**
	 * Action description.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $description = "This action will just search the ticket for the attendee but it won't redeam it.";

	/**
	 * Action hint
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $hint = 'This action requires <a target="_blank" href="https://woocommerce.com/products/woocommerce-order-barcodes/"> Woocommerce Order Barcodes</a> plugin to be installed.';


	/**
	 * Action title.
	 * The action title that will appear on admin dashboard.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $title = 'Search Ticket';


	/**
	 * Allowable merge tags.
	 * The allowable merge tags for this action
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	public $allowable_merge_tags = array(
		'first_name'   => array(
			'tag'         => '{first_name}',
			'description' => 'The user first name',
		),
		'last_name'    => array(
			'tag'         => '{last_name}',
			'description' => 'The user last name',
		),
		'user_email'   => array(
			'tag'         => '{user_email}',
			'description' => 'The user email',
		),
		'order_status' => array(
			'tag'         => '{order_status}',
			'description' => 'The order status',
		),
		'is_attended'  => array(
			'tag'         => '{is_attended}',
			'description' => 'Flag if the ticket is attended or not',
		),
	);


	/**
	 * Default invalid conditions.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $default_invalid_conditions = array(
		'ticket_not_found' => array(
			'title'                 => 'If ticket is not found',
			'default'               => true,
			'default_response_text' => 'Ticket Not found',
		),
	);

	/**
	 * Optional invalid conditions.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $optional_invalid_conditions = array(
		'ticket_already_redeamed'    => array(
			'title'                 => 'Invalidate if ticket is redeamed already?',
			'default'               => true,
			'default_response_text' => 'Ticket is already redeamed!',
		),
		'ticket_order_not_completed' => array(
			'title'                 => 'Invalidate if ticket purchase order is not completed',
			'default'               => true,
			'default_response_text' => 'Ticket order is not completed!',
		),
	);

	/**
	 * Process action.
	 * After processing any action we should set action data with a new value to be able to access it via handle_response method.
	 *
	 * @since 1.0.0
	 */
	public function process_action( $scan_data, $meta ) {

		global $wpdb;
		if ( ! is_plugin_active( 'woocommerce-order-barcodes/woocommerce-order-barcodes.php' ) ) {
			return array(
				'status' => 0,
				'text'   => 'Woocommerce Order Barcodes plugin is not installed!',
			);
		}
		// codereadr_get_logger()->debug( 'processing action', $meta );
		$scanned_ticket_id = esc_attr( $scan_data['tid'] );

		$ticket_id = absint( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_barcode_text', $scanned_ticket_id ) ) );

		if ( ! $ticket_id ) {
			return array(
				'status' => 0,
				'text'   => $meta['default_invalid_conditions']['ticket_not_found']['response_text'],
			);
		}

		$optional_invalid_conditions = $meta['optional_invalid_conditions'];
		if ( $optional_invalid_conditions['ticket_already_redeamed'] && $optional_invalid_conditions['ticket_already_redeamed']['checkbox'] ) {
			$is_attended = get_post_meta( $ticket_id, '_attended', true );
			if ( $is_attended ) {
				return array(
					'status' => 0,
					'text'   => $this->parse_custom_merge_tags(
						$ticket_id,
						$scan_data,
						$meta['optional_invalid_conditions']['ticket_already_redeamed']['response_text']
					),
				);
			}
		}
		if ( $optional_invalid_conditions['ticket_order_not_completed'] && $optional_invalid_conditions['ticket_order_not_completed']['checkbox'] ) {
			$order_id     = wp_get_post_parent_id( $ticket_id );
			$order_status = get_post_status( $order_id );
			if ( 'wc-completed' !== $order_status ) {
				return array(
					'status' => 0,
					'text'   => $this->parse_custom_merge_tags(
						$ticket_id,
						$scan_data,
						$meta['optional_invalid_conditions']['ticket_order_not_completed']['response_text']
					),
				);
			}
		}
		do_action( 'codereadr_before_success_response_for_search_action_for_wc_event_box', $ticket_id );
		$response_text = $this->parse_custom_merge_tags( $ticket_id, $scan_data, $meta['success_response_txt'] );
		return array(
			'status' => 1,
			'text'   => $response_text,
		);
	}


	/**
	 * Parse custom merge tags
	 *
	 * @since 1.0.0
	 */
	public function parse_custom_merge_tags( $post_id, $scan_data, $response_text ) {
		// Get ticket product ID.
		$product_id = get_post_meta( $post_id, '_product', true );

		// Get available fields from ticket product.
		$ticket_fields     = get_post_meta( $product_id, '_ticket_fields', true );
		$order_id          = wp_get_post_parent_id( $post_id );
		$order_status_name = 'N/A';
		$order_status      = get_post_status( $order_id );
		if ( $order_id ) {
				$order_status_name = wc_get_order_status_name( $order_status );
		}

		$first_name = '';
		$last_name  = '';
		$email      = '';
		foreach ( $ticket_fields as $field_key => $field ) {

			$ticket_meta = get_post_meta( $post_id, $field_key, true );
			switch ( $field['type'] ) {

				case 'first_name':
					$first_name = $ticket_meta;
					break;

				case 'last_name':
					$last_name = $ticket_meta;
					break;

				case 'email':
					$email = $ticket_meta;
					break;
			}
		}
		$is_attended = get_post_meta( $post_id, '_attended', true );
		if ( ! $is_attended ) {
			$is_attended = 'no';
		}
		$response_text = $this->parse_codereadr_merge_tags( $scan_data, $response_text );
		$response_text = str_replace( '{first_name}', $first_name, $response_text );
		$response_text = str_replace( '{last_name}', $last_name, $response_text );
		$response_text = str_replace( '{user_email}', $email, $response_text );
		$response_text = str_replace( '{order_status}', $order_status_name, $response_text );
		$response_text = str_replace( '{is_attended}', $is_attended, $response_text );

		return $response_text;

	}
}

if ( is_plugin_active( 'woocommerce-box-office/woocommerce-box-office.php' ) ) {
	Actions_Manager::instance()->register( new WC_Box_Office_Search_Action() );
}
