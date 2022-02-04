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
	 * Action title.
	 * The action title that will appear on admin dashboard.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $title;


	/**
	 * Action data.
	 * The action data that will prepered after processing.
	 *
	 * @var mixed
	 *
	 * @since 1.0.0
	 */
	public $action_data = null;

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
	 * Process action.
	 * After processing any action we should set action data with a new value to be able to access it via handle_response method.
	 *
	 * @since 1.0.0
	 */
	public function process_action() {

	}


	/**
	 * Handle response
	 * The method should return the response needed to be shown to the user and it is expected to parse the allowablecustom merge tags for this action.
	 *
	 * @since 1.0.0
	 */
	public function handle_response() {
		// we should be able here to access  $this->action_data.
		return null;
	}


}
