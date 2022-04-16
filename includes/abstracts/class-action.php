<?php
/**
 * Logging api: class Log
 * This class is forked from Woocommerce.
 *
 * @since 1.0.0
 * @package CodeReadr/Abstracts
 */

namespace CodeReadr\Abstracts;

class Action {

	/**
	 * Action name.
	 * It must be a unique name.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $name;

	/**
	 * Action description.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $description;

	/**
	 * Integration slug
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $integration_slug;


	/**
	 * Action title.
	 * The action title that will appear on admin dashboard.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $title;

	/**
	 * Allowable merge tags.
	 * The allowable merge tags for this action
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	public $allowable_merge_tags = array();

	/**
	 * Default invalid conditions.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	public $default_invalid_conditions = array();

	/**
	 * Optional Invalide conditions.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	public $optional_invalid_conditions = array();

	/**
	 * Process action.
	 *
	 * @since 1.0.0
	 *
	 * @param array $scan_data The scan data retrieved from CodeReadr.
	 *    $scan_data = [
	 *      'tid'     => (string) Scanned Ticked Id.
	 *      'sid' => (string) Service Id.
	 *    ].
	 * @param array $meta The action meta
	 *    $meta = [
	 *      'default_invalid_conditions'     => [
	 *          'ticket_not_found' => [
	 *              'response_text' => (string) The response text.
	 *          ] // This is just an example.
	 *      ]
	 *      'optional_invalid_conditions' => [
	 *          'ticket_already_redeamed' => [
	 *              'checkbox' => (bool) Is option checked or not.
	 *              'response_text' => (string) The response text.
	 *          ] // This is just an example.
	 *      ],
	 *      'success_response_txt' => (string) The success response text.
	 *    ].
	 *
	 * @return array $response The response
	 *    $response = [
	 *        'status' => (int) 0 for invalid response or 1 for valid
	 *        'text' => (string) The response text.
	 *    ]
	 */
	public function process_action( $scan_data, $meta ) {
		$response = array(
			'status' => 0,
			'text'   => 'valid response',
		);
		return $response;
	}

	/**
	 * Parse CodeReadr merge tags
	 *
	 * @since 1.0.0
	 */
	public function parse_codereadr_merge_tags( $scan_data, $response_text ) {
		$response_text = str_replace( '{tid}', $scan_data['tid'], $response_text );
		$response_text = str_replace( '{sid}', $scan_data['sid'], $response_text );
		return $response_text;
	}

}
