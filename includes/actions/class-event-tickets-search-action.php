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
class Event_Tickets_Search_Action extends Action {

	/**
	 * Action name.
	 * It must be a unique name.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $name = 'event-tickets-search-action';

	/**
	 * Integration slug
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $integration_slug = 'event-tickets';

	/**
	 * Action description.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $description = "This action will just search the ticket for the attendee but it won't redeam it.";

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
		'full_name'   => array(
			'tag'         => '{full_name}',
			'description' => 'The user full name',
		),
		'user_email'  => array(
			'tag'         => '{user_email}',
			'description' => 'The user email',
		),
		'is_attended' => array(
			'tag'         => '{is_attended}',
			'description' => 'Flag if the ticket is attended or not',
		),
		'event_id'    => array(
			'tag'         => '{event_id}',
			'description' => 'The event id',
		),
		'event_name'  => array(
			'tag'         => '{event_name}',
			'description' => 'The event name',
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
		codereadr_get_logger()->debug( 'processing event tickets search action', $meta );
		$ticket_id = $scan_data['tid'];
		$sql       = "select * from {$wpdb->prefix}postmeta where meta_value='" . $ticket_id . "'";

		$meta_row = $wpdb->get_row( $sql, ARRAY_A );
		if ( ! $meta_row ) {
			return array(
				'status' => 0,
				'text'   => $meta['default_invalid_conditions']['ticket_not_found']['response_text'],
			);
		}
		$security_code_key  = $meta_row['meta_key'];
		$attendee_id        = $meta_row['post_id'];
		$security_code_type = null;
		if ( class_exists( 'Tribe__Tickets_Plus__Attendee_Repository' ) ) {
			try {
				$tickets_attendee_rep = tribe( 'tickets.attendee-repository' );
				$security_code_keys   = $tickets_attendee_rep->security_code_keys();

				foreach ( $security_code_keys as $key => $value ) {
					if ( $value === $security_code_key ) {
						$security_code_type = $key;
						break;
					}
				}

				if ( ! $security_code_type ) {
					return array(
						'status' => 0,
						'text'   => 'Unknown security code',
					);
				} else {
					$event_meta_key = $tickets_attendee_rep->attendee_to_event_keys()[ $security_code_type ];
					$event_id       = get_post_meta( $attendee_id, $event_meta_key, true );
					$provider       = tribe_tickets_get_ticket_provider( $attendee_id );
					// $response_text  = $this->parse_custom_merge_tags( $event_id, $attendee_id, $provider, $scan_data, $meta['success_response_txt'] );
					$attendee = $provider->get_attendee( $attendee_id );

					// codereadr_get_logger()->debug( 'Attendee', $attendee );
					$optional_invalid_conditions = $meta['optional_invalid_conditions'];
					if ( $optional_invalid_conditions['ticket_already_redeamed'] && $optional_invalid_conditions['ticket_already_redeamed']['checkbox'] ) {
						if ( $attendee['check_in'] ) {
							return array(
								'status' => 0,
								'text'   => $this->parse_custom_merge_tags( $event_id, $attendee, $scan_data, $meta['optional_invalid_conditions']['ticket_already_redeamed']['response_text'] ),
							);
						}
					}
					if ( $optional_invalid_conditions['ticket_order_not_completed'] && $optional_invalid_conditions['ticket_order_not_completed']['checkbox'] ) {
						if ( 'completed' !== $attendee['order_status'] ) {
							return array(
								'status' => 0,
								'text'   => $this->parse_custom_merge_tags( $event_id, $attendee, $scan_data, $meta['optional_invalid_conditions']['ticket_order_not_completed']['response_text'] ),
							);
						}
					}

					do_action( 'codereadr_before_success_response_for_search_action_for_event_tickets_plugin', $provider, $attendee_id );
					return array(
						'status' => 1,
						'text'   => $this->parse_custom_merge_tags( $event_id, $attendee, $scan_data, $meta['success_response_txt'] ),
					);

				}
			} catch ( \Exception $e ) {
				return array(
					'status' => 0,
					'text'   => $e->getMessage,
				);
			}
		} else {
			return array(
				'status' => 0,
				'text'   => 'Tribe Tickets Plus last version isn\'t installed',
			);
		}
		// $optional_invalid_conditions = $meta['optional_invalid_conditions'];
		// if ( $optional_invalid_conditions['ticket_already_redeamed'] && $optional_invalid_conditions['ticket_already_redeamed']['checkbox'] ) {
		// $is_attended = get_post_meta( $meta_row['post_id'], '_attended', true );
		// if ( $is_attended ) {
		// return array(
		// 'status' => 0,
		// 'text'   => $this->parse_custom_merge_tags(
		// $meta_row['post_id'],
		// $scan_data,
		// $meta['optional_invalid_conditions']['ticket_already_redeamed']['response_text']
		// ),
		// );
		// }
		// }

		// do_action( 'codereadr_before_success_response_for_search_action_for_event_tickets_plugin', $meta_row['post_id'] );
		// $response_text = $this->parse_custom_merge_tags( $meta_row['post_id'], $scan_data, $meta['success_response_txt'] );
		// return array(
		// 'status' => 1,
		// 'text'   => $response_text,
		// );
	}


	/**
	 * Parse custom merge tags
	 *
	 * @since 1.0.0
	 */

	public function parse_custom_merge_tags( $event_id, $attendee, $scan_data, $response_text ) {
		$is_attended = 'yes';
		if ( ! $attendee['check_in'] ) {
			$is_attended = 'no';
		}

		$response_text = $this->parse_codereadr_merge_tags( $scan_data, $response_text );
		$response_text = str_replace( '{full_name}', $attendee['purchaser_name'], $response_text );
		$response_text = str_replace( '{user_email}', $attendee['purchaser_email'], $response_text );
		$response_text = str_replace( '{order_status}', $attendee['order_status'], $response_text );
		$response_text = str_replace( '{is_attended}', $is_attended, $response_text );
		$response_text = str_replace( '{event_id}', $event_id, $response_text );
		$response_text = str_replace( '{event_name}', get_the_title( $event_id ), $response_text );

		return $response_text;

	}
}

if ( is_plugin_active( 'event-tickets-plus/event-tickets-plus.php' ) && is_plugin_active( 'event-tickets/event-tickets.php' ) ) {
	add_filter(
		'codereadr_integrations',
		function( $integrations ) {
			$integrations['event-tickets'] = array(
				'title' => 'Event Tickets',
			);

			return $integrations;
		}
	);

	Actions_Manager::instance()->register( new Event_Tickets_Search_Action() );

}
