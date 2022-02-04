<?php
/**
 * Actions API: Actions_Manager class.
 *
 * @package CodeReadr
 * @since 1.0.0
 */

namespace CodeReadr\Managers;

use CodeReadr\Abstracts\Action;

/**
 * Core class used for interacting with block types.
 *
 * @since 1.0.0
 */
final class Actions_Manager {
	/**
	 * Registered Actions.
	 *
	 * @since 1.0.0
	 *
	 * @var Action[]
	 */
	private $registered_actions = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @var Actions_Manager|null
	 */
	private static $instance = null;

	/**
	 * Registers a block type.
	 *
	 * @since 1.0.0
	 *
	 * @param Action $block Action instance.
	 *
	 * @return Action the registered action on success, or false on failure
	 */
	public function register( Action $block ) {
		$action      = $block;
		$action_name = $action->name;

		if ( preg_match( '/[A-Z]+/', $action_name ) ) {
			$message = __( 'Action names must not contain uppercase characters.', 'codereadr' );
			_doing_it_wrong( __METHOD__, $message, '1.0.0' );

			return false;
		}

		if ( $this->is_registered( $action_name ) ) {
			/* translators: %s: Block name. */
			$message = sprintf( __( 'Action "%s" is already registered.', 'codereadr' ), $action_name );
			_doing_it_wrong( __METHOD__, $message, '1.0.0' );

			return false;
		}

		$this->registered_actions[ $action_name ] = $action;

		return $action;
	}

	/**
	 * Unregisters a block type.
	 *
	 * @since 1.0.0
	 *
	 * @param string|Action $type action name including namespace, or alternatively a
	 *                              complete Action instance.
	 *
	 * @return Action|false the unregistered action on success, or false on failure
	 */
	public function unregister( $type ) {

		if ( ! $this->is_registered( $type ) ) {
			/* translators: %s: Action name. */
			$message = sprintf( __( 'Action "%s" is not registered.', 'codereadr' ), $type );
			_doing_it_wrong( __METHOD__, $message, '1.0.0' );

			return false;
		}

		$unregistered_action = $this->registered_actions[ $type ];
		unset( $this->registered_actions[ $type ] );

		return $unregistered_action;
	}

	/**
	 * Retrieves a registered action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name action name including namespace.
	 *
	 * @return Action|null the registered action, or null if it is not registered
	 */
	public function get_registered( $name ) {
		if ( ! $this->is_registered( $name ) ) {
			return null;
		}

		return $this->registered_actions[ $name ];
	}

	/**
	 * Retrieves all registered block types.
	 *
	 * @since 1.0.0
	 *
	 * @return Action[] associative array of `$action_name => $action` pairs
	 */
	public function get_all_registered() : iterable {
		return $this->registered_actions;
	}

	/**
	 * Checks if a block type is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name block type name including namespace.
	 *
	 * @return bool true if the block type is registered, false otherwise
	 */
	public function is_registered( $name ) : bool {
		return isset( $this->registered_actions[ $name ] );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 1.0.0
	 *
	 * @return Blocks_Manager the main instance
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
