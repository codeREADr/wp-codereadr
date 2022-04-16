<?php
/**
 * Building custom integration for Codereadr
 */

use CodeReadr\Abstracts\Action;
use CodeReadr\Managers\Actions_Manager;


/**
* Let Codereadr know about my integration
* You should replace 'my-custom-integration' with your unique integration slug.
* And you should replace "My Custom Integration" with your integration title.
*/
add_filter(
	'codereadr_integrations',
	function( $integrations ) {
		$integrations['my-custom-integration'] = array(
			'title' => 'My Custom Integration',
		);

		return $integrations;
	}
);


/**
* Adding an action for this integration via the following action class
*/
if ( ! class_exists( 'Codereadr_My_Custom_Action' ) ) {
	class Codereadr_My_Custom_Action extends Action {

		/**
		 * Action name.
		 * It must be a unique name.
		 *
		 * @var string
		 */
		public $name = 'my-custom-action';


		/**
		 * Integration slug
		 * This should be the same slug for your integraion unique slug.
		 *
		 * @var string
		 */
		public $integration_slug = 'my-custom-integration';

		/**
		 * Action title.
		 * The action title that will appear on admin dashboard.
		 *
		 * @var string
		 */
		public $title = 'Redeam Ticket';

		/**
		 * Action description.
		 * This is the action description that will appear on admin dashboard.
		 *
		 * @var string
		 */
		public $description = 'This is my custom action description';

		/**
		 * Default invalid conditions.
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
		 * Allowable merge tags.
		 * The custom merge tags that are related to your action.
		 * The allowable merge tags for this action
		 *
		 * @var array
		 */
		public $allowable_merge_tags = array(
			'full_name'  => array(
				'tag'         => '{full_name}',
				'description' => 'The user full name',
			),
			'user_email' => array(
				'tag'         => '{user_email}',
				'description' => 'The user email',
			),
		);
		/**
		 * Process action.
		 * After processing any action we should set action data with a new value
		 * to be able to access it via handle_response method.
		 */
		public function process_action( $scan_data, $meta ) {
			$ticket_id = $scan_data['tid'];

			// Remove the hash from the following line and insert your query.
			// $is_ticket_found = Do  Your Query Here!
			if ( ! $is_ticket_found ) {
				return array(
					'status' => 0,
					'text'   => $meta['default_invalid_conditions']['ticket_not_found']['response_text'],
				);
			}

			$success_reponse_text = $meta['success_response_txt'];
			return array(
				'status' => 1,
				'text'   => $success_reponse_text,
			);

			// At all cases you must return an array with
			// "status" key with value of 0 in case on invalid response  and 1 for valid response.
			// And "text" for the response text.
		}
	}

	Actions_Manager::instance()->register( new Codereadr_My_Custom_Action() );
}
