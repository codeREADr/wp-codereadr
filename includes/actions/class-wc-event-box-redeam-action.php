<?php
/**
 * Action for Woocommerce Event Box
 *
 * @package Actions
 */

namespace CodeReadr;
use CodeReadr\Managers\Actions_Manager;
/**
 * This is an action class for Woocommerce Event Box plugin
 *
 * @since 1.0.0
 */
class WC_Event_Box_Redeam_Action extends WC_Event_Box_Search_Action {

	/**
	 * Action name.
	 * It must be a unique name.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $name = 'wc-event-box-redeam-action';


	/**
	 * Integration slug
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $integration_slug = 'wc-event-box';

	/**
	 * Action description.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $description = 'This action will redeam ticket for the attendee';

	/**
	 * Action title.
	 * The action title that will appear on admin dashboard.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $title = 'Redeam Ticket';


	/**
	 * Constructor
	 */
	public function __construct() {
		add_action(
			'codereadr_before_success_response_for_search_action_for_wc_event_box',
			function( $post_id ) {
				update_post_meta( $post_id, '_attended', 'yes' );
			}
		);
	}
}

if ( is_plugin_active( 'woocommerce-box-office/woocommerce-box-office.php' ) && is_plugin_active( 'woocommerce-order-barcodes/woocommerce-order-barcodes.php' ) ) {
	add_filter(
		'codereadr_integrations',
		function( $integrations ) {
			$integrations['wc-event-box'] = array(
				'title' => 'WC Event Box',
			);

			return $integrations;
		}
	);

	Actions_Manager::instance()->register( new WC_Event_Box_Redeam_Action() );
}
