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
class WC_Box_Office_Redeam_Action extends WC_Box_Office_Search_Action {

	/**
	 * Action name.
	 * It must be a unique name.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $name = 'wc-box-office-redeam-action';


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
	 * Process action.
	 *
	 * @since 1.0.0
	 */
	public function process_action( $scan_data, $meta ) {
		try {
			add_action(
				'codereadr_before_success_response_for_search_action_for_wc_event_box',
				function( $post_id ) {
					update_post_meta( $post_id, '_attended', 'yes' );
				}
			);
			return $this->parent_process_action( $scan_data, $meta );
		} catch ( \Exception $e ) {
			return array(
				'status' => 0,
				'text'   => $e->getMessage(),
			);
		}
	}

	public function parent_process_action( $scan_data, $meta ) {
		return parent::process_action( $scan_data, $meta );

	}
}

if ( is_plugin_active( 'woocommerce-box-office/woocommerce-box-office.php' ) ) {
	add_filter(
		'codereadr_integrations',
		function( $integrations ) {
			$integrations['wc-box-office'] = array(
				'title' => 'Woocommerce Box Office',
			);

			return $integrations;
		}
	);

	Actions_Manager::instance()->register( new WC_Box_Office_Redeam_Action() );
}
